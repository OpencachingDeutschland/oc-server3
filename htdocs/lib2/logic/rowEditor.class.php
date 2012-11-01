<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class rowEditor
{
	var $sTable;
	var $sAutoIncrementField = null;
	var $pk;     // (idx:name; type, default, nullable, value, insertfunction)
	var $fields; // (idx:name; type, default, nullable, value, changed, insertfunction)

	// status var
	var $bLoaded = false;
	var $bExist = false;
	var $bAddNew = false;

	/* primaryKey may be an array
	 */
	function __construct($sTable)
	{
		$this->pk = array();
		$this->fields = array();
		$this->sTable = $sTable;
	}

	function addPKInt($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_INT, 
		                           'default' => $nDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $nDefault,
		                           'insertfunction' => $nInsertFunction);

		if (($nInsertFunction & RE_INSERT_AUTOINCREMENT) == RE_INSERT_AUTOINCREMENT)
			$this->sAutoIncrementField = $sField;
	}

	function addPKFloat($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_FLOAT, 
		                           'default' => $nDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $nDefault,
		                           'insertfunction' => $nInsertFunction);
	}

	function addPKDouble($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_DOUBLE, 
		                           'default' => $nDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $nDefault,
		                           'insertfunction' => $nInsertFunction);
	}

	function addPKString($sField, $sDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_STRING, 
		                           'default' => $sDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $sDefault,
		                           'insertfunction' => $nInsertFunction);
	}

	function addPKBoolean($sField, $bDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_BOOLEAN, 
		                           'default' => $bDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $bDefault,
		                           'insertfunction' => $nInsertFunction);
	}

	function addPKDate($sField, $dDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->pk[$sField] = array('type' => RE_TYPE_DATE, 
		                           'default' => $dDefault, 
		                           'nullable' => $bNullable, 
		                           'value' => $dDefault,
		                           'insertfunction' => $nInsertFunction);
	}

	function addInt($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_INT, 
		                               'default' => $nDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $nDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function addFloat($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_FLOAT, 
		                               'default' => $nDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $nDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function addDouble($sField, $nDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_DOUBLE, 
		                               'default' => $nDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $nDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function addString($sField, $sDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_STRING, 
		                               'default' => $sDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $sDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function addBoolean($sField, $bDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_BOOLEAN, 
		                               'default' => $bDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $bDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function addDate($sField, $dDefault, $bNullable, $nInsertFunction=RE_INSERT_NOTHING)
	{
		$this->fields[$sField] = array('type' => RE_TYPE_DATE, 
		                               'default' => $dDefault, 
		                               'nullable' => $bNullable, 
		                               'value' => $dDefault, 
		                               'changed => false',
		                               'insertfunction' => $nInsertFunction);
	}

	function removePK($sField)
	{
		unset($this->pk[$sField]);
	}

	function removeField($sField)
	{
		unset($this->fields[$sField]);
	}

	/* PKValues may be an string, indized or ordered array
	 */
	function load($PKValues)
	{
		$this->pSetPK($PKValues);

		$this->bLoaded = true;
		$this->bAddNew = false;
		$this->bExist = false;

		$rs = sql($this->pBuildSelect());
		if (!$r = sql_fetch_assoc($rs))
		{
			$this->bExist = false;
			return false;
		}

		// assign values
		foreach ($this->fields AS $k => $field)
		{
			$this->fields[$k]['value'] = $this->pFormatValue($this->fields[$k]['type'], $r[$k]);
			$this->fields[$k]['changed'] = false;
		}

		$this->bExist = true;

		return true;
	}

	function addNew($PKValues)
	{
		$this->pSetPK($PKValues);

		$this->bLoaded = true;
		$this->bExist = false;
		$this->bAddNew = true;
	}

	function exist()
	{
		return $this->bExist;
	}

	function pSetPK($PKValues)
	{
		$this->pResetValues();

		foreach ($this->pk AS $k => $field)
		{
			$this->pk[$k]['value'] = $field['default'];
		}

		if (is_array($PKValues))
		{
			foreach ($PKValues AS $k => $v)
			{
				$pkKey = $this->pGetPKKey($k);
				$this->pk[$pkKey]['value'] = $this->pFormatValue($this->pk[$pkKey]['type'], $v);
			}
		}
		else
		{
			$pkKey = $this->pGetPKKey(0);
			$this->pk[$pkKey]['value'] = $this->pFormatValue($this->pk[$pkKey]['type'], $PKValues);
		}
	}

	function pGetPKKey($index)
	{
		if (isset($this->pk[$index]))
			return $index;

		$i = 0;
		foreach ($this->pk AS $k => $v)
		{
			if ($i == $index)
				return $k;
			$i++;
		}
	}

	function pResetValues()
	{
		foreach ($this->fields AS $k => $field)
		{
			$this->fields[$k]['value'] = $field['default'];
			$this->fields[$k]['changed'] = false;
		}
	}

	function pFormatValue($type, $value)
	{
		if ($value === null)
			return null;

		if ($type == RE_TYPE_INT)
			$value = (int)$value+0;
		else if ($type == RE_TYPE_FLOAT)
			$value = $value+0;
		else if ($type == RE_TYPE_DOUBLE)
			$value = $value+0;
		else if ($type == RE_TYPE_BOOLEAN)
			$value = (($value+0) != 0);
		else if ($type == RE_TYPE_DATE)
		{
			if (!is_numeric($value))
				$value = strtotime($value);
		}

		return $value;
	}

	function pFormatValueSql($type, $value)
	{
		if ($type == RE_TYPE_INT)
			$value = (int)$value+0;
		else if ($type == RE_TYPE_FLOAT)
			$value = $value+0;
		else if ($type == RE_TYPE_DOUBLE)
			$value = $value+0;
		else if ($type == RE_TYPE_BOOLEAN)
			$value = (($value+0) != 0) ? 1 : 0;
		else if ($type == RE_TYPE_DATE)
		{
			if (!is_numeric($value))
				$value = strtotime($value);

			$value = strftime(DB_DATE_FORMAT, $value);
		}

		return $value;
	}

	function pBuildSelect()
	{
		$fselect = array();
		$sql = 'SELECT ';
		foreach ($this->fields AS $k => $field)
		{
			$fselect[] = '`' . sql_escape($k) . '`';
		}
		$sql .= join(', ', $fselect);

		$sql .= ' FROM `' . sql_escape($this->sTable) . '`';
		$sql .= ' WHERE ' . $this->pBuildPK();

		return $sql;
	}

	function pBuildPK()
	{
		$fwhere = array();
		foreach ($this->pk AS $k => $field)
		{
			if ($field['value'] === null)
				$fwhere[] = 'ISNULL(`' . sql_escape($k) . '`)';
			else
				$fwhere[] = '`' . sql_escape($k) . '`=\'' . sql_escape($field['value']) . '\'';
		}
		return join(' AND ', $fwhere);
	}

	function getValue($sField)
	{
		if (isset($this->pk[$sField]))
			return $this->pk[$sField]['value'];

		return $this->fields[$sField]['value'];
	}

	function getDefault($sField)
	{
		return $this->fields[$sField]['default'];
	}

	function getChanged($sField)
	{
		return $this->fields[$sField]['changed'];
	}

	function getAnyChanged()
	{
		foreach ($this->fields AS $field)
			if ($field['changed'] == true)
				return true;
	}

	function setValue($sField, $sValue)
	{
		if ($this->bLoaded == false || ($this->bAddNew == false && $this->bExist == false))
			return false;

		$sFormatedValue = $this->pFormatValue($this->fields[$sField]['type'], $sValue);
		if ($this->fields[$sField]['type'] == RE_TYPE_FLOAT)
		  // Direct float comparison is deprecated and can result in last-digit errors.
		  // Floats in OC database are only used for reasonably large numbers like coordinates,
		  // waylengths and time estimates, so using a fixed epsilon threshold is safe:
		  $changed = (abs($sFormatedValue - $this->fields[$sField]['value'])  >= 1e-13);
		else
		  $changed = ($sFormatedValue != $this->fields[$sField]['value']);
		if ($changed)
		{
			$this->fields[$sField]['value'] = $sFormatedValue;
			$this->fields[$sField]['changed'] = true;
		}
		return true;
	}

	function save()
	{
		if ($this->bLoaded == false || ($this->bAddNew == false && $this->bExist == false))
			return false;

		if ($this->bAddNew == true)
		{
			// INSERT
			$sql = $this->pBuildInsert();

			if ($sql != '')
			{
				sql($sql);
				if (sql_affected_rows() == 0)
					return false;
			}
			else
				return true;

			if ($this->sAutoIncrementField != null)
			{
				$nInsertId = sql_insert_id();

				$this->pk[$this->sAutoIncrementField]['value'] = $nInsertId;

				if (isset($this->fields[$this->sAutoIncrementField]))
					$this->fields[$this->sAutoIncrementField]['value'] = $nInsertId;
			}

			$pkv = array();
			foreach ($this->pk AS $k => $v)
			{
				$pkv[$k] = $this->pk[$k]['value'];
			}
			$this->load($pkv);

			return true;
		}
		else
		{
			// UPDATE
			$sql = $this->pBuildUpdate();
			
			if ($sql != '')
			{
				$rs = sql($sql);
				if (sql_affected_rows($rs) == 0)
					return false;
			}
			else
				return true;

			foreach ($this->fields AS $k => $field)
			{
				$this->fields[$k]['changed'] = false;
			}

			return true;
		}
	}

	function reload()
	{
		$pkv = array();
		foreach ($this->pk AS $k => $v)
		{
			$pkv[$k] = $this->pk[$k]['value'];
		}
		$this->load($pkv);
	}

	function pBuildInsert()
	{
		$sql = 'INSERT IGNORE INTO `' . sql_escape($this->sTable) . '` (';
		
		$sFields = array();
		$sValues = array();

		foreach ($this->pk AS $k => $field)
		{
			if (isset($this->fields[$k]))
				continue;

			if ($this->sAutoIncrementField == $k)
				continue;

			if (($field['insertfunction'] & RE_INSERT_IGNORE) == RE_INSERT_IGNORE)
				continue;

			$sFields[] = '`' . sql_escape($k) . '`';

			if ((($field['insertfunction'] & RE_INSERT_OVERWRITE) == RE_INSERT_OVERWRITE) || (($field['changed'] == false) && ($field['insertfunction'] != RE_INSERT_NOTHING)))
			{
				if (($field['insertfunction'] & RE_INSERT_UUID) == RE_INSERT_UUID)
					$sValues[] = 'UUID()';
				else if (($field['insertfunction'] & RE_INSERT_NOW) == RE_INSERT_NOW)
					$sValues[] = 'NOW()';
				else
					$sValues[] = 'NULL';
			}
			else
			{
				if ($field['value'] === null)
					$sValues[] = 'NULL';
				else
					$sValues[] = '\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
			}
		}

		foreach ($this->fields AS $k => $field)
		{
			if (($field['insertfunction'] & RE_INSERT_IGNORE) == RE_INSERT_IGNORE)
				continue;

			$sFields[] = '`' . sql_escape($k) . '`';

			if ((($field['insertfunction'] & RE_INSERT_OVERWRITE) == RE_INSERT_OVERWRITE) || (($field['changed'] == false) && ($field['insertfunction'] != RE_INSERT_NOTHING)))
			{
				if (($field['insertfunction'] & RE_INSERT_UUID) == RE_INSERT_UUID)
					$sValues[] = 'UUID()';
				else if (($field['insertfunction'] & RE_INSERT_NOW) == RE_INSERT_NOW)
					$sValues[] = 'NOW()';
				else
					$sValues[] = 'NULL';
			}
			else
			{
				if ($field['value'] === null)
					$sValues[] = 'NULL';
				else
					$sValues[] = '\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
			}
		}
		$sql .= join(', ', $sFields);
		$sql .= ') VALUES (';
		$sql .= join(', ', $sValues);
		$sql .= ')';

		return $sql;
	}

	function pBuildUpdate()
	{
		$sql = 'UPDATE IGNORE `' . sql_escape($this->sTable) . '` SET ';

		$sSet = '';
		foreach ($this->fields AS $k => $field)
		{
			if ($field['changed'] == true)
			{
				if ($sSet != '') $sSet .= ', ';

				if ($field['value'] === null)
					$sSet .= '`' . sql_escape($k) . '`=NULL';
				else
					$sSet .= '`' . sql_escape($k) . '`=\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
			}
		}

		if ($sSet == '')
			return '';

		$sql .= $sSet;
		$sql .= ' WHERE ';
		$sql .= $this->pBuildPK();

		return $sql;
	}

	function saveField($field)
	{
		if ($this->bLoaded == false || $this->bExist == false || $this->bAddNew == true)
			return false;

		if ($this->fields[$field]['changed'] == false)
			return true;

		if ($this->fields[$field]['value'] === null)
			$sSet = '`' . sql_escape($field) . '`=NULL';
		else
			$sSet = '`' . sql_escape($field) . '`=\'' . sql_escape($this->pFormatValueSql($this->fields[$field]['type'], $this->fields[$field]['value'])) . '\'';

		$sql = 'UPDATE `' . sql_escape($this->sTable) . '` SET ' . $sSet;
		$sql .= ' WHERE ';
		$sql .= $this->pBuildPK();

		sql($sql);
		if (sql_affected_rows() == 0)
			return false;

		$this->fields[$field]['changed'] = false;

		return true;
	}
}
?>