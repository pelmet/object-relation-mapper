<?php

class ObjectRelationMapper_Listing_Table
{
	protected $colCount = 0;

	protected $dataSource = NULL;
	protected $columns = Array();
	protected $headers = Array();

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $tableData = Array();

	public function __construct()
	{
		$this->template = __DIR__.'/Templates/table.phtml';
	}

	/**
	 * Prida data source
	 * @param ObjectRelationMapper_Listing_Connector_Interface $source
	 */
	public function addDataSource(ObjectRelationMapper_Listing_Connector_Interface $source)
	{
		$this->dataSource = $source;
	}

	/**
	 * Vrati column k dodatecnym upravam
	 * @param $alias
	 * @return ObjectRelationMapper_Listing_Column_Interface
	 */
	public function &getColumn($alias)
	{
		return $this->columns[$alias];
	}

	/**
	 * @param $alias
	 * @return ObjectRelationMapper_Listing_Column_Header
	 */
	public function &getHeader($alias)
	{
		return $this->headers[$alias];
	}

	/**
	 * Prida Column do stacku
	 * @param $header
	 * @param ObjectRelationMapper_Listing_Column_Interface $column
	 * @param null $alias
	 */
	public function addColumn($header, ObjectRelationMapper_Listing_Column_Interface $column, $alias = NULL)
	{
		if(is_null($alias)){
			$alias = $this->colCount;
			$this->colCount++;
		}

		$this->headers[$alias] = new ObjectRelationMapper_Listing_Column_Header($header);
		$this->columns[$alias] = $column;
	}

	/**
	 * Upravy columny do citelne podoby
	 * @throws Exception
	 */
	protected function translateData()
	{
		if(is_null($this->dataSource)){
			throw new Exception('Cant create table from empty dataset');
		}

		if(empty($this->headers) || empty($this->columns)){
			throw new Exception('You need to define at least one column');
		}

		foreach($this->dataSource as $source){
			$rowData = new ObjectRelationMapper_Listing_Internal_Row();
			$rowData->setSource($source);
			foreach($this->columns as $column){
				$col = clone $column;
				$rowData->addColumn($col);
			}
			$this->tableData[] = $rowData;
		}
	}

	public function render()
	{
		$this->translateData();

		ob_start();
		include($this->template);
		return ob_get_clean();
	}
}