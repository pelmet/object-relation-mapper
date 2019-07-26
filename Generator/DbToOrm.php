<?php

namespace ObjectRelationMapper\Generator;

use ObjectRelationMapper\Connector\IConnector;

/**
 * Class DbToOrm
 *
 * @example new Generator_DbToOrm(new Connector_ESDB(), 'salary', 'master', 'ORM_Salary', 'ORM_Base', TEMP_DIR, 'sa_');
 */
class DbToOrm
{
    /** @var array */
    protected $columns = [];

    /** @var array */
    protected $children = [];

    /** @var array */
    protected $ormText = [];

    /** @var array */
    protected static $typeTrans = [
        'char'       => 'string',
        'varchar'    => 'string',
        'tinytext'   => 'string',
        'text'       => 'string',
        'blob'       => 'string',
        'mediumtext' => 'string',
        'mediumblob' => 'string',
        'longtext'   => 'string',
        'longblob'   => 'string',
        'tinyint'    => 'int',
        'smallint'   => 'int',
        'mediumint'  => 'int',
        'int'        => 'int',
        'bigint'     => 'int',
        'float'      => 'decimal',
        'double'     => 'decimal',
        'decimal'    => 'decimal',
        'datetime'   => 'timestamp',
    ];

    /** @var array */
    protected static $propertyTrans = [
        'decimal'   => 'float',
        'timestamp' => 'string',
        'date'      => 'string',
        'enum'      => 'string'
    ];

    protected $usedPhpAliases = [];

    /**
     * Generator ORMka
     * @param IConnector $connector
     * @param string     $dbTable
     * @param string     $serverAlias
     * @param string     $ormName
     * @param string     $extendingOrm
     * @param string     $path
     * @param string     $colPrefix
     * @param string     $namespace
     * @param bool       $childPhpAliasFromColumn
     * @throws \Exception
     */
    public function __construct(IConnector $connector, $dbTable, $serverAlias, $ormName, $extendingOrm, $path, $colPrefix = null, $namespace = null, $childPhpAliasFromColumn = false)
    {
        $describe = $connector->query('DESCRIBE ' . $dbTable, [], $serverAlias);
        if (empty($describe)) {
            throw new \Exception('Table not exist!');
        }
        $primary = null;
        foreach ($describe as $column) {
            preg_match('/^([^\(]*)?\(?([0-9]+)?,?([0-9]+)?\)?(.*)?$/i', $column['Type'], $matches);
            if (!empty($matches[3])) {
                $length = (isset($matches[2]) ? $matches[2] : 0) . ',' . $matches[3];
            } elseif ($matches[1] == 'enum') {
                $lgt = [];
                preg_match_all('/\'(.*?)\'/i', $matches[4], $lgt);
                $length = 'Array(' . implode(', ', $lgt[0]) . ')';
            } elseif (stripos($matches[1], 'text') !== false) {
                $length = 65536;
            } else {
                $length = (isset($matches[2]) ? $matches[2] : 0);
            }
            if ($column['Key'] == 'PRI') {
                $primary = $column['Field'];
                if (is_null($colPrefix) && preg_match('/^([a-z]+_).*/', $primary, $prefixMatch)) {
                    $colPrefix = $prefixMatch[1];
                }
            }
            $this->addColumn($column['Field'], strtolower($matches[1]), $length);
        }

        $schema = $connector->query('SELECT SCHEMA()', [], $serverAlias);
        if (empty($schema)) {
            throw new \Exception('Default schema not selected, cannnot determine relations!');
        }
        $schemaName = current(current($schema));
        foreach ($connector->query("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME = '$dbTable' AND TABLE_SCHEMA = '$schemaName' AND REFERENCED_TABLE_NAME IS NOT NULL;", [], $serverAlias) as $child) {
            $this->addChild($child['COLUMN_NAME'], $child['REFERENCED_TABLE_NAME'], $child['REFERENCED_COLUMN_NAME'], $childPhpAliasFromColumn);
        }

        $this->addOrmLine('<?php');
        $this->addOrmLine('');

        if (!empty($namespace)) {
            $this->addOrmLine('namespace ' . $namespace . ';');
            $this->addOrmLine('');
        }

        if (false !== strpos($extendingOrm, '\\')) {
            $this->addOrmLine('use ' . $extendingOrm . ';');
            $this->addOrmLine('');
            $exParts = explode('\\', $extendingOrm);
            $extendingOrm = end($exParts);

        }

        $this->addOrmLine('/**');

        foreach ($this->columns as $columnName => $columnInfo) {
            $this->addOrmLine(' * @property ' . $columnInfo['property'] . ' $' . $this->toCamelCase($columnName, $colPrefix));
        }
        foreach ($this->children as $value) {
            $this->addOrmLine(' * @property ' . $value['ormName'] . '[] $' . $value['phpAlias']);
            $this->addOrmLine(' * @method ' . $value['ormName'] . '|NULL getFirst' . ucfirst($value['phpAlias']) . '()');
        }
        $this->addOrmLine(' **/');

        $this->addOrmLine('');
        $this->addOrmLine('class ' . $ormName . ' extends ' . $extendingOrm);
        $this->addOrmLine('{');
        $this->addOrmLine('    function setUp()');
        $this->addOrmLine('    {');
        foreach ($this->columns as $columnName => $columnInfo) {
            if (empty($primary)) {
                $primary = $columnName;
            }

            if ($columnInfo['type'] == 'enum') {
                $this->addOrmLine('        $this->addColumn(\'' . $columnName . '\', \'' . $this->toCamelCase($columnName, $colPrefix) . '\', \'' . $columnInfo['type'] . '\', ' . $columnInfo['length'] . ');');
            } else {
                $this->addOrmLine('        $this->addColumn(\'' . $columnName . '\', \'' . $this->toCamelCase($columnName, $colPrefix) . '\', \'' . $columnInfo['type'] . '\', \'' . $columnInfo['length'] . '\');');
            }
        }

        if (!empty($this->children)) {
            $this->addOrmLine('');
            foreach ($this->children as $childName => $childInfo) {
                $this->addOrmLine('        $this->addChild(\'' . $childInfo['ormName'] . '\', \'' . $childInfo['phpAlias'] . '\', \'' . $childInfo['localKey'] . '\', \'' . $childInfo['foreignKey'] . '\');');
            }
        }

        $this->addOrmLine('');
        $this->addOrmLine('        $this->setConfigDbPrimaryKey(\'' . $primary . '\');');
        $this->addOrmLine('        $this->setConfigDbTable(\'' . $dbTable . '\');');
        $this->addOrmLine('        $this->setConfigDbServer(\'' . $serverAlias . '\');');
        $this->addOrmLine('        $this->setConfigObject(static::class);');
        $this->addOrmLine('    }');
        $this->addOrmLine('}');

        $path = $path . '/' . str_replace('\\', '/', $namespace);
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
        file_put_contents($path . '/' . $ormName . '.php', $this->getOrm());
    }

    /**
     * @param string $columnName
     * @param string $columnType
     * @param string $columnLength
     */
    protected function addColumn($columnName, $columnType, $columnLength)
    {
        $type = $this->getColumnPhpType($columnType);
        $this->columns[$columnName] = ['type' => $type, 'length' => $columnLength, 'property' => $this->getPhpPropertyType($type)];
    }

    /**
     * @param string $columnName
     * @param string $childTable
     * @param string $childColumnName
     * @param bool   $childPhpAliasFromColumn
     */
    protected function addChild($columnName, $childTable, $childColumnName, $childPhpAliasFromColumn = false)
    {
        $phpAliasSource = $childTable;
        if ($childPhpAliasFromColumn) {
            $phpAliasSource = preg_replace('#^(.+)_id$#i', '\\1', $columnName);
        }
        $phpAlias = $this->toCamelCase($phpAliasSource, '');
        if (!isset($this->usedPhpAliases[$phpAlias])) {
            $this->usedPhpAliases[$phpAlias] = 1;
        } else {
            $this->usedPhpAliases[$phpAlias]++;
            $phpAlias = $phpAlias . $this->usedPhpAliases[$phpAlias];
        }
        $this->children[$columnName] = [
            'ormName'    => $this->tableToClassName($childTable),
            'phpAlias'   => $phpAlias,
            'localKey'   => $columnName,
            'foreignKey' => $childColumnName
        ];
    }

    public static function getColumnPhpType($column)
    {
        if (isset(self::$typeTrans[$column])) {
            return self::$typeTrans[$column];
        } else {
            return $column;
        }
    }

    public static function getPhpPropertyType($column)
    {
        if (isset(self::$propertyTrans[$column])) {
            return self::$propertyTrans[$column];
        } else {
            return $column;
        }
    }

    protected function toCamelCase($column, $tablePrefix)
    {
        $column = str_ireplace($tablePrefix, '', $column);
        $parts = explode('_', $column);

        return lcfirst(implode('', array_map('ucfirst', $parts)));
    }

    protected function tableToClassName($tableName)
    {
        $parts = explode('_', $tableName);
        $max = count($parts) - 1;
        if (DbToOrm::isPhpReserved(end($parts))) {
            $parts[$max] = $parts[$max-1] . ucfirst($parts[$max]);
        }

        return '\\ORM\\' . implode('\\', array_map('ucfirst', $parts));
    }


    protected function addOrmLine($line)
    {
        $this->ormText[] = $line;
    }

    protected function getOrm()
    {
        return implode("\n", $this->ormText);
    }

    public static function isPhpReserved($string)
    {
        $keywords = [
            '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone',
            'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare',
            'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for',
            'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof',
            'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected',
            'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset',
            'use', 'var', 'while', 'xor',
            'parent', 'self', 'static', 'list',
            '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__'];
        return in_array($string, $keywords);
    }
}