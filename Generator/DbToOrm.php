<?php

namespace ObjectRelationMapper\Generator;

use ObjectRelationMapper\Connector\IConnector;

/**
 * Class DbToOrm
 * @example new Generator_DbToOrm(new Connector_ESDB(), 'salary', 'master', 'ORM_Salary', 'ORM_Base', TEMP_DIR, 'sa_');
 */
class DbToOrm
{
	protected $columns;
	protected $firstCol;
	protected $ormText = Array();

	protected $typeTrans = Array(
        'char' => 'string',
        'varchar' => 'string',
        'tinytext' => 'string',
        'text' => 'string',
        'blob' => 'string',
        'mediumtext' => 'string',
        'mediumblob' => 'string',
        'longtext' => 'string',
        'longblob' => 'string',
        'tinyint' => 'int',
        'smallint' => 'int',
        'mediumint' => 'int',
        'int' => 'int',
        'bigint' => 'int',
        'float' => 'decimal',
        'double' => 'decimal',
        'decimal' => 'decimal'
	);

    protected $propertyTrans = Array(
        'decimal' => 'float',
        'date' => 'string'
    );

    /**
     * Generator ORMka
     * @param IConnector $connector
     * @param string $dbTable
     * @param string $serverAlias
     * @param string $ormName
     * @param string $extendingOrm
     * @param string $path
     * @param string $colPrefix
     * @param string $namespace
     * @throws \Exception
     */
	public function __construct(IConnector $connector, $dbTable, $serverAlias, $ormName, $extendingOrm, $path, $colPrefix,$namespace = null)
	{
		$describe = $connector->query('DESCRIBE ' . $dbTable, Array(), $serverAlias);
        if(empty($describe)){
            throw new \Exception('Table not exist!');
        }
		foreach ($describe as $column) {
			preg_match('/^([^\(]*)?\(?([0-9]+)?,?([0-9]+)?\)?(.*)?$/i', $column['Type'], $matches);
            if(isset($matches[3])){
                $length = (isset($matches[2]) ? $matches[2] : 0).','.$matches[3];
            }else{
                $length = (isset($matches[2]) ? $matches[2] : 0);
            }

			$this->addColumn($column['Field'], strtolower($matches[1]), $length);
		}

		$this->addOrmLine('<?php');
		$this->addOrmLine('');

        if(!empty($namespace)){
            $this->addOrmLine('namespace '.$namespace.';');
            $this->addOrmLine('');
        }

		$this->addOrmLine('/**');

		foreach ($this->columns as $columnName => $columnInfo) {
			$this->addOrmLine('* @property ' . $columnInfo['property'] . ' ' . $this->toCamelCase($columnName, $colPrefix));
		}
		$this->addOrmLine('**/');

		$this->addOrmLine('');
		$this->addOrmLine('class ' . $ormName . ' extends ' . $extendingOrm);
		$this->addOrmLine('{');
		$this->addOrmLine('    function setUp()');
		$this->addOrmLine('    {');
		$first = false;
		foreach ($this->columns as $columnName => $columnInfo) {
			if ($first == false) {
				$this->firstCol = $columnName;
				$first = true;
			}
			$this->addOrmLine('        $this->addColumn(\'' . $columnName . '\', \'' . $this->toCamelCase($columnName, $colPrefix) . '\', \'' . $columnInfo['type'] . '\', \'' . $columnInfo['length'] . '\');');
		}

		$this->addOrmLine('');
		$this->addOrmLine('        $this->setConfigDbPrimaryKey(\'' . $this->firstCol . '\');');
		$this->addOrmLine('        $this->setConfigDbTable(\'' . $dbTable . '\');');
		$this->addOrmLine('        $this->setConfigDbServer(\'' . $serverAlias . '\');');
		$this->addOrmLine('        $this->setConfigObject(__CLASS__);');
		$this->addOrmLine('    }');
		$this->addOrmLine('}');

		file_put_contents($path . '/' . $ormName . '.php', $this->getOrm());
	}

	protected function addColumn($columnName, $columnType, $columnLength)
	{
        $type = $this->getColumnPhpType($columnType);
		$this->columns[$columnName] = Array('type' => $type, 'length' => $columnLength, 'property' => $this->getPhpPropertyType($type));
	}

	protected function getColumnPhpType($column)
	{
		if (isset($this->typeTrans[$column])) {
			return $this->typeTrans[$column];
		} else {
			return $column;
		}
	}

    protected function getPhpPropertyType($column)
    {
        if (isset($this->propertyTrans[$column])) {
            return $this->propertyTrans[$column];
        } else {
            return $column;
        }
    }

	protected function toCamelCase($column, $tablePrefix)
	{
		$column = str_ireplace($tablePrefix, '', $column);

		$e = explode('_', $column);

		if (!empty($e)) {
			foreach ($e as &$value) {
				$value = ucfirst($value);
			}
			return lcfirst(implode('', $e));
		} else {
			return $column;
		}
	}

	protected function addOrmLine($line)
	{
		$this->ormText[] = $line;
	}

	protected function getOrm()
	{
		return implode("\n", $this->ormText);
	}
}