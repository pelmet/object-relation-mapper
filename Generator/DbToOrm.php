<?php

/**
 * Class ObjectRelationMapper_Generator_DbToOrm
 * @example new ObjectRelationMapper_Generator_DbToOrm(new ObjectRelationMapper_Connector_ESDB(), 'salary', 'master', 'ORM_Salary', 'ORM_Base', TEMP_DIR);
 */
class ObjectRelationMapper_Generator_DbToOrm
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
	 * @param ObjectRelationMapper_Connector_Interface $connector
	 * @param string $dbTable
	 * @param string $serverAlias
	 * @param string $ormName
	 * @param string $extendingOrm
	 * @param string $path
	 */
	public function __construct(ObjectRelationMapper_Connector_Interface $connector, $dbTable, $serverAlias, $ormName, $extendingOrm, $path)
	{
		$describe = $connector->query('DESCRIBE '.$dbTable, Array(), $serverAlias);

		foreach($describe as $column){
			preg_match('/^(.*?)(\((.*)\))?$/', $column['Type'], $matches);
			$this->addColumn($column['Field'], $matches[1], (isset($matches[3]) ? $matches[3] : 0));
		}

		$this->addOrmLine('<?php');
		$this->addOrmLine('');
		$this->addOrmLine('/**');

		foreach($this->columns as $columnName => $columnInfo){
			$this->addOrmLine('* @property string '.$columnName);
		}
		$this->addOrmLine('**/');

		$this->addOrmLine('');
		$this->addOrmLine('class '.$ormName. ' extends '. $extendingOrm);
		$this->addOrmLine('{');
		$this->addOrmLine('    function setUp()');
		$this->addOrmLine('    {');
		$first = false;
		foreach($this->columns as $columnName => $columnInfo){
			if($first == false){
				$this->firstCol = $columnName;
				$first = true;
			}
			$this->addOrmLine('        $this->addColumn(\''.$columnName.'\', \''.$columnName.'\', \''.$columnInfo['type'].'\', \''.$columnInfo['length'].'\');');
		}

		$this->addOrmLine('');
		$this->addOrmLine('');
		$this->addOrmLine('        $this->setConfigDbPrimaryKey(\''.$this->firstCol.'\');');
		$this->addOrmLine('        $this->setConfigDbTable(\''.$dbTable.'\');');
		$this->addOrmLine('        $this->setConfigDbServer(\''.$serverAlias.'\');');
		$this->addOrmLine('        $this->setConfigObject(__CLASS__);');
		$this->addOrmLine('    }');
		$this->addOrmLine('}');

		file_put_contents($path . '/' .$ormName.'.php', $this->getOrm());
	}

	protected function addColumn($columnName, $columnType, $columnLength)
	{
		$this->columns[$columnName] = Array('type' => $this->getColumnPhpType($columnType), 'length' => $columnLength);
	}

	protected function getColumnPhpType($column)
	{
		if(isset($this->typeTrans[$column])){
			return $this->typeTrans[$column];
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