<?php

namespace App\Entity\Traits;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

trait Identifiable
{
//    protected ?string $tableName;
//    protected ?string $entityName;
//    protected ?string $idField;
//    protected ?string $idColumn;
//    protected ?string $addedField;
//    protected ?string $addedColumn;
//    protected ?string $updatedField;
//    protected ?string $updatedColumn;
//    protected ?string $deletedField;
//    protected ?string $deletedColumn;
//    protected ?string $id;
//    protected ?string $path;
//    protected ?array $properties;
//    protected ?array $fields;
//
//    protected function loadProperties(): void
//    {
//        if(!$this->hasProperties()) $this->getProperties();
//    }
//
//    protected function hasProperties(): ?bool
//    {
//        return isset($this->properties) && count($this->properties) > 0;
//    }
//
//    protected function getProperties(): ?array
//    {
//        if(!$this->hasProperties()) {
//            $class_parts = explode('\\', get_called_class());
//            $this->entityName = end($class_parts);
//            $this->tableName = strtolower($this->entityName);
//            $this->idField = "{$this->tableName}Id";
//            $this->idColumn = "{$this->tableName}_id";
//            $this->addedField = "{$this->tableName}Added";
//            $this->addedColumn = "{$this->tableName}_added";
//            $this->updatedField = "{$this->tableName}Updated";
//            $this->updatedColumn = "{$this->tableName}_updated";
//            $this->deletedField = "{$this->tableName}Deleted";
//            $this->deletedColumn = "{$this->tableName}_deleted";
//            $this->id = $this->{$this->idField};
//            $this->path = strtolower(end($class_parts));
//
//            // extract property info (property names, property types, etc.) using reflection
//            $propertyInfo = new PropertyInfoExtractor([new ReflectionExtractor()], [new ReflectionExtractor()]);
//            $this->properties = $propertyInfo->getProperties(get_called_class());
//
//            foreach($this->properties as $prop) {
//                // upon reaching the first of the "calculated" properties (entityName), cease adding to the fields list
//                if($prop == "entityName") break;
//
//                $propTypes = $propertyInfo->getTypes(get_called_class(), $prop);
//
//                if(!is_array($propTypes) || !is_object($propTypes[0])) continue;
//                $propType = array_values((array) $propertyInfo->getTypes(get_called_class(), $prop)[0]);
//
//                // get field properties from the ORM (Doctrine) annotations in the entity class
//                $docProps = ((new AnnotationReader())->getPropertyAnnotations((new \ReflectionClass(get_called_class()))->getProperty($prop)));
//                $docProps = is_array($docProps) && isset($docProps[0]) ? $docProps[0] : $docProps;
//
//                /*
//                 * $propType is in the following format:
//                 *
//                 * Symfony\Component\PropertyInfo\Type Object (
//                 *  [builtinType:Symfony\Component\PropertyInfo\Type:private] => string
//                 *  [nullable:Symfony\Component\PropertyInfo\Type:private] => 1
//                 *  [class:Symfony\Component\PropertyInfo\Type:private] =>
//                 *  [collection:Symfony\Component\PropertyInfo\Type:private] =>
//                 *  [collectionKeyType:Symfony\Component\PropertyInfo\Type:private] =>
//                 *  [collectionValueType:Symfony\Component\PropertyInfo\Type:private] =>
//                 * )
//                 *
//                 * $docProps is in one of the following formats:
//                 *
//                 * Doctrine\ORM\Mapping\Column Object (
//                 *  [name] => site_id
//                 *  [type] => string
//                 *  [length] => 8
//                 *  [precision] => 0
//                 *  [scale] => 0
//                 *  [unique] =>
//                 *  [nullable] =>
//                 *  [options] => Array ( )
//                 *  [columnDefinition] =>
//                 * )
//                 *
//                 * Doctrine\ORM\Mapping\ManyToOne Object (
//                 *  [targetEntity] => Firm
//                 *  [cascade] =>
//                 *  [fetch] => LAZY
//                 *  [inversedBy] =>
//                 * )
//                 */
//
//                $value = $this->$prop;
//                if(is_object($value)) {
//                    switch(get_class($value)) {
//                        case "DateTime":
//                            $value = $value->format("Y-m-d");
//                            break;
//                        default:
//                            continue;
//                    } // end switch
//                } // end if
//
//                $docProps->type = isset($docProps->targetEntity) && !isset($docProps->type) ? "join" : $docProps->type;
//
//                $this->fields[$prop] = [
//                    "name" => $prop, // actual property name, from the entity
//                    "label" => $this->getFieldLabel($prop), // a label for the field
//                    "type" => $this->getFieldType($propType), // data type or class of the field, see Symfony\Component\PropertyInfo\Type
//                    "columnType" => $docProps->type,
//                    "inputType" => $this->getInputType($docProps->type), // type of HTML input that should be used to edit the field
//                    "value" => $value, // record's value for this field
//                    "listable" => (bool) !preg_match("/^" . $this->tableName . "(id|password|index|deleted)$/i", $prop) && $docProps->type != "text", // whether the field should be displayed on lists
//                    "editable" => is_scalar($value) && (bool) !preg_match("/^" . $this->tableName . "(id|index|added|updated|deleted)$/i", $prop), // whether the field should be edited
//                    "required" => !$propType[1] // whether a value is required when editing
//                ];
//            } // end foreach
//        } // end if
//
//        return $this->properties;
//    }
//
//    /**
//     * get the camelCase short class name of the entity
//     *
//     * @return string|null
//     */
//    public function getEntityName(): ?string
//    {
//        if(!isset($this->entityName)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->entityName;
//    }
//
//    /**
//     * alias of self::getEntityName
//     *
//     * @return string|null
//     */
//    public function entityName(): ?string
//    {
//        return $this->getEntityName();
//    }
//
//    /**
//     * get the database table name of the entity
//     *
//     * @return string|null
//     */
//    public function getTableName(): ?string
//    {
//        if(!isset($this->tableName)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->tableName;
//    }
//
//    /**
//     * alias of self::getTableName
//     *
//     * @return string|null
//     */
//    public function tableName(): ?string
//    {
//        return $this->getTableName();
//    }
//
//
//    /**
//     * get the camelCase property name for the id (primary key) of the entity
//     *
//     * @return string|null
//     */
//    public function getIdField(): ?string
//    {
//        if(!isset($this->idField)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->idField;
//    }
//
//    /**
//     * alias of self::getIdField
//     *
//     * @return string|null
//     */
//    public function idField(): ?string
//    {
//        return $this->getIdField();
//    }
//
//    /**
//     * get the camelCase property name for the added (record created timestamp) of the entity
//     *
//     * @return string|null
//     */
//    public function getAddedField(): ?string
//    {
//        if(!isset($this->addedField)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->addedField;
//    }
//
//    /**
//     * alias of self::getAddedField
//     *
//     * @return string|null
//     */
//    public function addedField(): ?string
//    {
//        return $this->getAddedField();
//    }
//
//    /**
//     * get the camelCase property name for the updated (record updated timestamp) of the entity
//     *
//     * @return string|null
//     */
//    public function getUpdatedField(): ?string
//    {
//        if(!isset($this->updatedField)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->updatedField;
//    }
//
//    /**
//     * alias of self::getUpdatedField
//     *
//     * @return string|null
//     */
//    public function updatedField(): ?string
//    {
//        return $this->getUpdatedField();
//    }
//
//    /**
//     * get the camelCase property name for the deleted (record soft-deleted timestamp) of the entity
//     *
//     * @return string|null
//     */
//    public function getDeletedField(): ?string
//    {
//        if(!isset($this->deletedField)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->deletedField;
//    }
//
//    /**
//     * alias of self::getDeletedField
//     *
//     * @return string|null
//     */
//    public function deletedField(): ?string
//    {
//        return $this->getDeletedField();
//    }
//
//    /**
//     * get the id (primary key) of the entity
//     *
//     * @return string|null
//     */
//    public function getId(): ?string
//    {
//        if(!isset($this->id)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->id;
//    }
//
//    /**
//     * alias of self::getId
//     *
//     * @return string|null
//     */
//    public function id(): ?string
//    {
//        return $this->getId();
//    }
//
//    /**
//     * set the id (primary key) of the entity
//     *
//     * @param  string $id
//     * @return self
//     */
//    public function setId(string $id): self {
//        $this->{$this->getIdField()} = $id;
//        return $this;
//    }
//
//    /**
//     * get the path (lowercase class short name) of the entity
//     *
//     * @return string|null
//     */
//    public function getPath(): ?string
//    {
//        if(!isset($this->path)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->path;
//    }
//
//    /**
//     * alias of self::getPath
//     *
//     * @return string|null
//     */
//    public function path(): ?string
//    {
//        return $this->getPath();
//    }
//
//    /**
//     * get record added date
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function getAdded($format = "Y-m-d"): ?string
//    {
//        return $this->{"get" . $this->entityName . "Added"}()->format($format);
//    }
//
//
//    /**
//     * alias of self::getAdded
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function added($format = "Y-m-d"): ?string
//    {
//        return $this->getAdded($format);
//    }
//
//    /**
//     * get record updated date
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function getUpdated($format = "Y-m-d"): ?string
//    {
//        return $this->{"get" . $this->entityName . "Updated"}()->format($format);
//    }
//
//    /**
//     * alias of self::getUpdated
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function updated($format = "Y-m-d"): ?string
//    {
//        return $this->getUpdated($format);
//    }
//
//    /**
//     * get record soft-deleted date
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function getDeleted($format = "Y-m-d"): ?string
//    {
//        return $this->{"get" . $this->entityName . "Deleted"}()->format($format);
//    }
//
//    /**
//     * alias of self::getDeleted
//     *
//     * @param string $format return date format (e.g. Y-m-d)
//     * @return string|null
//     */
//    public function deleted($format = "Y-m-d"): ?string
//    {
//        return $this->getDeleted($format);
//    }
//
//    /**
//     * get the fields available in the entity
//     *
//     * @return array|null
//     */
//    public function getFields(): ?array
//    {
//        if(!isset($this->fields)) {
//            $this->loadProperties();
//        } // end if
//
//        return $this->fields;
//    }
//
//    /**
//     * alias of self::getFields
//     *
//     * @return array|null
//     */
//    public function fields(): ?array
//    {
//        return $this->getFields();
//    }

    /**
     * get the field(s) that most appropriately form a label for the entity
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        if(!isset($this->label)) {
            // property name fragments to check for, in preferential order
            $labelTokens = ["label", "name", "num", "fname", "comp", "company", "title", "id"];

            foreach($labelTokens as $token) {
                if(isset($this->{$token})) {
                    $this->label = $this->{$token};
                    break;
                } // end if
            } // end foreach
        } // end if

        if(isset($this->label))
            return $this->label;
        else
            return null;
    }

    /**
     * alias of self::getLabel
     *
     * @return string|null
     */
    public function label(): ?string
    {
        return $this->getLabel();
    }


//    /**
//     * get a label for a given field/property name
//     *
//     * @param  string $prop
//     * @return string
//     */
//    protected function getFieldLabel(string $prop): ?string {
//        return preg_replace("/" . $this->tableName . "/", "", $prop);
//    } // end function getFieldLabel
//
//    /**
//     * get a type for a given field/property
//     *
//     * @param  array $propType
//     * @return string
//     */
//    protected function getFieldType(array $propType): ?string {
//        return isset($propType[2]) ? $propType[2] : $propType[0];
//    } // end function getFieldType
//
//    /**
//     * get the HTML input type based on the database column type (e.g. DB "text" => HTML "textarea")
//     *
//     * @param  string $columnType
//     * @return string
//     */
//    protected function getInputType(string $columnType): ?string {
//        switch(strtolower($columnType)) {
//            case "varchar":
//            case "char":
//                return "text";
//                break;
//            case "text":
//            case "mediumtext":
//            case "longtext":
//                return "textarea";
//                break;
//            case "boolean":
//                return "checkbox";
//                break;
//            default:
//                return "text";
//        } // end switch
//    } // end function getInputType
} // end trait Identifiable
