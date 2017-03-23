<?php
/***************************************************************************
 * for license information see doc/license.txt
 ***************************************************************************/

/**
 * Class rowEditor
 */
class rowEditor
{
    public $sTable;
    public $sAutoIncrementField = null;
    public $pk; // (idx:name; type, default, nullable, value, insertfunction)
    public $fields; // (idx:name; type, default, nullable, value, changed, insertfunction)

    // status var
    public $bLoaded = false;
    public $bExist = false;
    public $bAddNew = false;

    /**
     * primaryKey may be an array
     *
     * @param string $sTable
     */
    public function __construct($sTable)
    {
        $this->pk = [];
        $this->fields = [];
        $this->sTable = $sTable;
    }

    /**
     * @param string $sField
     * @param $nDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addPKInt($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->pk[$sField] = [
            'type' => RE_TYPE_INT,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'insertfunction' => $nInsertFunction
        ];

        if (($nInsertFunction & RE_INSERT_AUTOINCREMENT) == RE_INSERT_AUTOINCREMENT) {
            $this->sAutoIncrementField = $sField;
        }
    }

    /**
     * @param $sField
     * @param $nDefault
     * @param $bNullable
     * @param int $nInsertFunction
     */
    public function addPKFloat($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->pk[$sField] = [
            'type' => RE_TYPE_FLOAT,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param $sField
     * @param $nDefault
     * @param $bNullable
     * @param int $nInsertFunction
     */
    public function addPKDouble($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->pk[$sField] = [
            'type' => RE_TYPE_DOUBLE,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param $sField
     * @param $sDefault
     * @param $bNullable
     * @param int $nInsertFunction
     */
    public function addPKString($sField, $sDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        if (($nInsertFunction & RE_INSERT_AUTOUUID) == RE_INSERT_AUTOUUID) {
            die('rowEditor: RE_INSERT_AUTOUUID not supported for primary key fields');
        }

        $this->pk[$sField] = [
            'type' => RE_TYPE_STRING,
            'default' => $sDefault,
            'nullable' => $bNullable,
            'value' => $sDefault,
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param $sField
     * @param $bDefault
     * @param $bNullable
     * @param int $nInsertFunction
     */
    public function addPKBoolean($sField, $bDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->pk[$sField] = [
            'type' => RE_TYPE_BOOLEAN,
            'default' => $bDefault,
            'nullable' => $bNullable,
            'value' => $bDefault,
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param $sField
     * @param $dDefault
     * @param $bNullable
     * @param int $nInsertFunction
     */
    public function addPKDate($sField, $dDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->pk[$sField] = [
            'type' => RE_TYPE_DATE,
            'default' => $dDefault,
            'nullable' => $bNullable,
            'value' => $dDefault,
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param $nDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addInt($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_INT,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param integer $nDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addFloat($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_FLOAT,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param integer $nDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addDouble($sField, $nDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_DOUBLE,
            'default' => $nDefault,
            'nullable' => $bNullable,
            'value' => $nDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param $sDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addString($sField, $sDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_STRING,
            'default' => $sDefault,
            'nullable' => $bNullable,
            'value' => $sDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param $bDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addBoolean($sField, $bDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_BOOLEAN,
            'default' => $bDefault,
            'nullable' => $bNullable,
            'value' => $bDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param string $sField
     * @param $dDefault
     * @param boolean $bNullable
     * @param int $nInsertFunction
     */
    public function addDate($sField, $dDefault, $bNullable, $nInsertFunction = RE_INSERT_NOTHING)
    {
        $this->fields[$sField] = [
            'type' => RE_TYPE_DATE,
            'default' => $dDefault,
            'nullable' => $bNullable,
            'value' => $dDefault,
            'changed => false',
            'insertfunction' => $nInsertFunction
        ];
    }

    /**
     * @param $sField
     */
    public function removePK($sField)
    {
        unset($this->pk[$sField]);
    }

    /**
     * @param $sField
     */
    public function removeField($sField)
    {
        unset($this->fields[$sField]);
    }

    /* PKValues may be an string, indized or ordered array
     */
    /**
     * @param $PKValues
     *
     * @return bool
     */
    public function load($PKValues)
    {
        $this->pSetPK($PKValues);

        $this->bLoaded = true;
        $this->bAddNew = false;
        $this->bExist = false;

        $rs = sql($this->pBuildSelect());
        if (!$r = sql_fetch_assoc($rs)) {
            $this->bExist = false;

            return false;
        }

        // assign values
        foreach ($this->fields as $k => $field) {
            $this->fields[$k]['value'] = $this->pFormatValue($this->fields[$k]['type'], $r[$k]);
            $this->fields[$k]['changed'] = false;
        }

        $this->bExist = true;

        return true;
    }

    /**
     * @param $PKValues
     */
    public function addNew($PKValues)
    {
        $this->pSetPK($PKValues);

        $this->bLoaded = true;
        $this->bExist = false;
        $this->bAddNew = true;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return $this->bExist;
    }

    /**
     * @param mixed $PKValues
     */
    public function pSetPK($PKValues)
    {
        $this->pResetValues();

        foreach ($this->pk as $k => $field) {
            $this->pk[$k]['value'] = $field['default'];
        }

        if (is_array($PKValues)) {
            foreach ($PKValues as $k => $v) {
                $pkKey = $this->pGetPKKey($k);
                $this->pk[$pkKey]['value'] = $this->pFormatValue($this->pk[$pkKey]['type'], $v);
            }
        } else {
            $pkKey = $this->pGetPKKey(0);
            $this->pk[$pkKey]['value'] = $this->pFormatValue($this->pk[$pkKey]['type'], $PKValues);
        }
    }

    /**
     * @param $index
     *
     * @return int|string
     */
    public function pGetPKKey($index)
    {
        if (isset($this->pk[$index])) {
            return $index;
        }

        $i = 0;
        foreach ($this->pk as $k => $v) {
            if ($i == $index) {
                return $k;
            }
            $i++;
        }
    }

    /**
     *
     */
    public function pResetValues()
    {
        foreach ($this->fields as $k => $field) {
            $this->fields[$k]['value'] = $field['default'];
            $this->fields[$k]['changed'] = false;
        }
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|false|int|null
     */
    public function pFormatValue($type, $value)
    {
        if ($value === null) {
            return null;
        }

        if ($type == RE_TYPE_INT) {
            $value = (int)$value + 0;
        } elseif ($type == RE_TYPE_FLOAT) {
            $value = $value + 0;
        } elseif ($type == RE_TYPE_DOUBLE) {
            $value = $value + 0;
        } elseif ($type == RE_TYPE_BOOLEAN) {
            $value = (($value + 0) != 0);
        } elseif ($type == RE_TYPE_DATE) {
            if (!is_numeric($value)) {
                $value = strtotime($value);
            }
        }

        return $value;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return false|int|string
     */
    public function pFormatValueSql($type, $value)
    {
        if ($type == RE_TYPE_INT) {
            $value = (int)$value + 0;
        } elseif ($type == RE_TYPE_FLOAT) {
            $value = $value + 0;
        } elseif ($type == RE_TYPE_DOUBLE) {
            $value = $value + 0;
        } elseif ($type == RE_TYPE_BOOLEAN) {
            $value = (($value + 0) != 0) ? 1 : 0;
        } elseif ($type == RE_TYPE_DATE) {
            if (!is_numeric($value)) {
                $value = strtotime($value);
            }

            $value = strftime(DB_DATE_FORMAT, $value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function pBuildSelect()
    {
        $fSelect = [];
        $sql = 'SELECT ';
        foreach ($this->fields as $k => $field) {
            $fSelect[] = '`' . sql_escape($k) . '`';
        }
        $sql .= join(', ', $fSelect);

        $sql .= ' FROM `' . sql_escape($this->sTable) . '`';
        $sql .= ' WHERE ' . $this->pBuildPK();

        return $sql;
    }

    /**
     * @return string
     */
    public function pBuildPK()
    {
        $fWhere = [];
        foreach ($this->pk as $k => $field) {
            if ($field['value'] === null) {
                $fWhere[] = 'ISNULL(`' . sql_escape($k) . '`)';
            } else {
                $fWhere[] = '`' . sql_escape($k) . '`=\'' . sql_escape($field['value']) . '\'';
            }
        }

        return join(' AND ', $fWhere);
    }

    /**
     * @param string $sField
     *
     * @return mixed
     */
    public function getValue($sField)
    {
        if (isset($this->pk[$sField])) {
            return $this->pk[$sField]['value'];
        }

        return $this->fields[$sField]['value'];
    }

    /**
     * @param $sField
     *
     * @return mixed
     */
    public function getDefault($sField)
    {
        return $this->fields[$sField]['default'];
    }

    /**
     * @param string $sField
     *
     * @return mixed
     */
    public function getChanged($sField)
    {
        return $this->fields[$sField]['changed'];
    }

    /**
     * @return boolean|null
     */
    public function getAnyChanged()
    {
        foreach ($this->fields as $field) {
            if ($field['changed'] == true) {
                return true;
            }
        }
    }

    /**
     * @param string $sField
     * @param $sValue
     * @return bool
     */
    public function setValue($sField, $sValue)
    {
        if ($this->bLoaded == false || ($this->bAddNew == false && $this->bExist == false)) {
            return false;
        }

        $sFormatedValue = $this->pFormatValue($this->fields[$sField]['type'], $sValue);
        if ($this->fields[$sField]['type'] == RE_TYPE_FLOAT) {
            // Direct float comparison is deprecated and can result in last-digit errors.
            // Floats in OC database are only used for reasonably large numbers like coordinates,
            // waylengths and time estimates, so using a fixed epsilon threshold is safe:
            $changed = (abs($sFormatedValue - $this->fields[$sField]['value']) >= 1e-12);
        } else {
            $changed = ($sFormatedValue != $this->fields[$sField]['value']) || ($this->fields[$sField]['nullable'] && (($sFormatedValue === null) != ($this->fields[$sField]['value'] === null)));
        }
        if ($changed) {
            $this->fields[$sField]['value'] = $sFormatedValue;
            $this->fields[$sField]['changed'] = true;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->bLoaded == false || ($this->bAddNew == false && $this->bExist == false)) {
            return false;
        }

        if ($this->bAddNew == true) {
            // INSERT
            $sql = $this->pBuildInsert();

            if ($sql != '') {
                sql($sql);
                if (sql_affected_rows() == 0) {
                    return false;
                }
            } else {
                return true;
            }

            if ($this->sAutoIncrementField != null) {
                $nInsertId = sql_insert_id();

                $this->pk[$this->sAutoIncrementField]['value'] = $nInsertId;

                if (isset($this->fields[$this->sAutoIncrementField])) {
                    $this->fields[$this->sAutoIncrementField]['value'] = $nInsertId;
                }
            }

            /* reload the record to get the actual stored values
             * (inserted values maybe truncated by mysql or trigger could modify values)
             */
            $pkv = [];
            foreach ($this->pk as $k => $v) {
                $pkv[$k] = $this->pk[$k]['value'];
            }
            $this->load($pkv);

            return true;
        } else {
            // UPDATE
            $sql = $this->pBuildUpdate();

            if ($sql != '') {
                $rs = sql($sql);
                // @bug wrong method signature
                if (sql_affected_rows($rs) == 0) {
                    return false;
                }
            } else {
                return true;
            }

            foreach ($this->fields as $k => $field) {
                $this->fields[$k]['changed'] = false;
            }

            return true;
        }
    }

    public function reload()
    {
        $pkv = [];
        foreach ($this->pk as $k => $v) {
            $pkv[$k] = $this->pk[$k]['value'];
        }
        $this->load($pkv);
    }

    /**
     * @return string
     */
    public function pBuildInsert()
    {
        $sql = 'INSERT IGNORE INTO `' . sql_escape($this->sTable) . '` (';

        $sFields = [];
        $sValues = [];

        foreach ($this->pk as $k => $field) {
            if (isset($this->fields[$k])) {
                continue;
            }

            if ($this->sAutoIncrementField == $k) {
                continue;
            }

            if (($field['insertfunction'] & RE_INSERT_IGNORE) == RE_INSERT_IGNORE) {
                continue;
            }

            $sFields[] = '`' . sql_escape($k) . '`';

            if ((($field['insertfunction'] & RE_INSERT_OVERWRITE) == RE_INSERT_OVERWRITE) || (($field['changed'] == false) && ($field['insertfunction'] != RE_INSERT_NOTHING))) {
                if (($field['insertfunction'] & RE_INSERT_NOW) == RE_INSERT_NOW) {
                    $sValues[] = 'NOW()';
                } else {
                    $sValues[] = 'NULL';
                }
            } else {
                if ($field['value'] === null) {
                    $sValues[] = 'NULL';
                } else {
                    $sValues[] = '\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
                }
            }
        }

        foreach ($this->fields as $k => $field) {
            if (($field['insertfunction'] & RE_INSERT_IGNORE) == RE_INSERT_IGNORE) {
                continue;
            }

            $sFields[] = '`' . sql_escape($k) . '`';

            if ((($field['insertfunction'] & RE_INSERT_OVERWRITE) == RE_INSERT_OVERWRITE) || (($field['changed'] == false) && ($field['insertfunction'] != RE_INSERT_NOTHING))) {
                if (($field['insertfunction'] & RE_INSERT_NOW) == RE_INSERT_NOW) {
                    $sValues[] = 'NOW()';
                } else {
                    $sValues[] = 'NULL';
                }
            } else {
                if ($field['value'] === null) {
                    $sValues[] = 'NULL';
                } else {
                    $sValues[] = '\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
                }
            }
        }
        $sql .= join(', ', $sFields);
        $sql .= ') VALUES (';
        $sql .= join(', ', $sValues);
        $sql .= ')';

        return $sql;
    }

    /**
     * @return string
     */
    public function pBuildUpdate()
    {
        $sql = 'UPDATE IGNORE `' . sql_escape($this->sTable) . '` SET ';

        $sSet = '';
        foreach ($this->fields as $k => $field) {
            if ($field['changed'] == true) {
                if ($sSet != '') {
                    $sSet .= ', ';
                }

                if ($field['value'] === null) {
                    $sSet .= '`' . sql_escape($k) . '`=NULL';
                } else {
                    $sSet .= '`' . sql_escape($k) . '`=\'' . sql_escape($this->pFormatValueSql($field['type'], $field['value'])) . '\'';
                }
            }
        }

        if ($sSet == '') {
            return '';
        }

        $sql .= $sSet;
        $sql .= ' WHERE ';
        $sql .= $this->pBuildPK();

        return $sql;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function saveField($field)
    {
        if ($this->bLoaded == false || $this->bExist == false || $this->bAddNew == true) {
            return false;
        }

        if ($this->fields[$field]['changed'] == false) {
            return true;
        }

        if ($this->fields[$field]['value'] === null) {
            $sSet = '`' . sql_escape($field) . '`=NULL';
        } else {
            $sSet = '`' . sql_escape($field) . '`=\'' . sql_escape($this->pFormatValueSql($this->fields[$field]['type'], $this->fields[$field]['value'])) . '\'';
        }

        $sql = 'UPDATE `' . sql_escape($this->sTable) . '` SET ' . $sSet;
        $sql .= ' WHERE ';
        $sql .= $this->pBuildPK();

        sql($sql);
        if (sql_affected_rows() == 0) {
            return false;
        }

        $this->fields[$field]['changed'] = false;

        return true;
    }
}
