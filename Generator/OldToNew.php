<?php

namespace ObjectRelationMapper\Generator;
use ObjectRelationMapper\Connector\IConnector;

/**
 * Class Generator_OldToNew
 */
class OldToNew
{
	protected $columns;
	protected $firstCol;
	protected $ormText = Array();
	protected $connector;

	protected $files = Array();

	protected $typeTrans = Array(
		'int' => 'int',
		'smallint' => 'int',
		'tinyint' => 'int',
		'varchar' => 'string',
		'char' => 'string',
		'text' => 'string'
	);

	/**
	 * Generator ORMka
	 */
	public function __construct($oldPath, $newPath, IConnector $connector)
	{
		$this->readDir($oldPath);

		$this->connector = $connector;

		system("rm -rf ".escapeshellarg($newPath));
		foreach($this->files as $file){
			$this->generateORM($file, str_ireplace($oldPath, $newPath . '/', $file));
		}
	}

	protected function readDir($dir)
	{
		foreach(array_diff(scandir($dir), Array('.', '..')) as $file){
			if(is_dir($dir . '/' . $file)){
				$this->readDir($dir . '/' .  $file . '/');
			} else {
				$this->files[] = $dir . $file;
			}
		}
	}

	protected function generateORM($oldFile, $newFile)
	{
		$this->ormText = Array();
		$this->columns = Array();

		$file = file_get_contents($oldFile);
		if(preg_match('/abstract\sclass\s(.*)\sextends(.*)/i', $file)){
			@mkdir(dirname($newFile), 0777, true);
			file_put_contents($newFile, $file);
			return false;
		}

		preg_match('/class\s(.*?)\sextends(.*?)\\n/i', $file, $matches);

		include_once($oldFile);
		$orm = new $matches[1];
		$extend = $matches[2];

		$describe = $this->connector->query('DESCRIBE '. $orm->getTable(), Array(), $orm->config('server'));
		$describe = array_column($describe, 'Type', 'Field');

		foreach($orm->getAllDbFields() as $column){
			if(isset($describe[$column])){
				preg_match('/^(.*?)(\((.*)\))?(\sunsigned)?$/', $describe[$column], $matches);
				$this->addColumn($column, $matches[1], (isset($matches[3]) ? $matches[3] : 0));
			} else {
				$this->addColumn($column, 'string', 500);
			}
		}

		$this->addOrmLine('<?php');
		$this->addOrmLine('');
		$this->addOrmLine('/**');

		foreach($this->columns as $columnName => $columnInfo){
			$this->addOrmLine('* @property '.$columnInfo['type'].' '.$orm->getAlias($columnName));
		}
		$this->addOrmLine('**/');

		$this->addOrmLine('');
		$this->addOrmLine('class '.$orm->config('object'). ' extends '. trim($extend));
		$this->addOrmLine('{');
		$this->addOrmLine('    function setUp()');
		$this->addOrmLine('    {');

		$first = false;
		foreach($this->columns as $columnName => $columnInfo){
			if($first == false){
				$this->firstCol = $columnName;
				$first = true;
			}
			$this->addOrmLine('        $this->addColumn(\''.$columnName.'\', \''.$orm->getAlias($columnName).'\', \''.$columnInfo['type'].'\', \''.$columnInfo['length'].'\');');
		}

		$this->addOrmLine('');
		$this->addOrmLine('');

		if(!empty($orm->config('child'))){
			foreach($orm->config('child') as $childId => $child){
				$this->addOrmLine('        $this->addChild(\''.$child['object'].'\', \''.$child['name'].'\', \''.$child[0]['localKey'].'\', \''.$child[0]['foreignKey'].'\');');
			}
		}

		$this->addOrmLine('');
		$this->addOrmLine('');

		$this->addOrmLine('        $this->setConfigDbPrimaryKey(\''.$orm->config('primaryKey').'\');');
		$this->addOrmLine('        $this->setConfigDbTable(\''.$orm->getTable().'\');');
		$this->addOrmLine('        $this->setConfigDbServer(\''.$orm->config('server').'\');');
		$this->addOrmLine('        $this->setConfigObject(__CLASS__);');
		$this->addOrmLine('    }');
		$this->addOrmLine('}');

		@mkdir(dirname($newFile), 0777, true);
		file_put_contents($newFile, $this->getOrm());
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

	protected function toCamelCase($column, $tablePrefix)
	{
		$column = str_ireplace($tablePrefix, '', $column);

		$e = explode('_', $column);

		if(!empty($e)){
			foreach($e as &$value){
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