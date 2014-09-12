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
		'int' => 'int',
		'varchar' => 'string',
		'char' => 'string',
		'text' => 'string'
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
	 */
	public function __construct(IConnector $connector, $dbTable, $serverAlias, $ormName, $extendingOrm, $path, $colPrefix)
	{
		$describe = $connector->query('DESCRIBE ' . $dbTable, Array(), $serverAlias);

		foreach ($describe as $column) {
			preg_match('/^(.*?)(\((.*)\))?$/', $column['Type'], $matches);
			$this->addColumn($column['Field'], $matches[1], (isset($matches[3]) ? $matches[3] : 0));
		}

		$this->addOrmLine('<?php');
		$this->addOrmLine('');
		$this->addOrmLine('/**');

		foreach ($this->columns as $columnName => $columnInfo) {
			$this->addOrmLine('* @property ' . $columnInfo['type'] . ' ' . $this->toCamelCase($columnName, $colPrefix));
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
		$this->columns[$columnName] = Array('type' => $this->getColumnPhpType($columnType), 'length' => $columnLength);
	}

	protected function getColumnPhpType($column)
	{
		if (isset($this->typeTrans[$column])) {
			return $this->typeTrans[$column];
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