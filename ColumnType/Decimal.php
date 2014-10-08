<?php

namespace ObjectRelationMapper\ColumnType;

class Decimal extends AColumn implements IColumn
{
	public function generateDbLine()
	{
		return $this->col . ' DECIMAL(' . $this->length . ') ';
	}

	/**
	 * Zvaliduje danou hodnotu a vrati true/false
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		return (is_numeric($value) && $this->checkDecimalLength($value));
	}

	private function checkDecimalLength($value)
	{
		if (preg_match('/^([0-9]+)[\.,;\/\\\:]{1}([0-9]+)$/i', $this->length, $matches) && !preg_match('/^[0-9]{0,' . $matches[1] . '}([\.,]{1}[0-9]{0,' . $matches[2] . '})?$/', (string)$value)) {
			return false;
		}
		return true;
	}

	/**
	 * float čísla se mění v PDO na string a je nahrazena tečka za čárku, což databáze nepochopí a odstraní hodnoty za čárkou protože nejsou int typu
	 * @param $value
	 * @return mixed
	 */
	public function getSanitezedPDOValue($value)
	{
		return str_replace(',', '.', strval($value));
	}
}