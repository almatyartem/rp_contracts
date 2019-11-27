<?php

namespace ApiSdk;

class StructureApi
{
    /**
     * @var array
     */
    protected $structure = [];

    /**
     * StructureApi constructor.
     * @param array $structure
     */
    public function __construct(array $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return array|null
     */
    protected function loadStructure() : ?array
    {
        if($content = file_get_contents(base_path('config/structure.json')))
        {
            return json_decode($content, true);
        }

        return null;
    }

    /**
     * @return array
     */
    public function getStructure() : ?array
    {
        return $this->structure;
    }

    /**
     * @return array
     */
    public function getAllEntities() : array
    {
        return array_keys($this->getStructure());
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityColumns(string $entity) : array
    {
        return $this->getStructure()[$entity]['columns'] ?? [];
    }

    /**
     * @param string $entity
     * @return string
     */
    public function getEntityTable(string $entity) : string
    {
        return $this->getStructure()[$entity]['table'] ?? '';
    }

    /**
     * @param string $entity
     * @param string $field
     * @return array
     */
    public function getEntityFieldConfig(string $entity, string $field) : array
    {
        return $this->getEntityColumns($entity)[$field] ?? [];
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityRelations(string $entity) : array
    {
        return $this->getStructure()[$entity]['relations'] ?? [];
    }

    /**
     * @param string $entity
     * @param string $name
     * @return array
     */
    public function getRelationByName(string $entity, string $name) : array
    {
        $relations = $this->getEntityRelations($entity);

        return $relations[$name] ?? [];
    }

    /**
     * @param string $entity
     * @param string|array $type
     * @return array
     */
    public function getRelationsByType(string $entity, $type) : array
    {
        if(!is_array($type))
        {
            $type = [$type];
        }
        return array_filter($this->getEntityRelations($entity), function($relationConfig) use ($type)
        {
            return in_array($relationConfig[0],$type);
        });
    }

    /**
     * @param string $entity
     * @param string $field
     * @return array
     */
    public function getBelongsToRelationsByField(string $entity, string $field) : array
    {
        return array_filter($this->getEntityRelations($entity), function($relationConfig) use ($field)
        {
            return ($relationConfig[0] == 'belongsTo' and $relationConfig[2] == $field);
        });
    }

    /**
     * @param string $entity
     * @param string $relatedEntity
     * @return array
     */
    public function getBelongsToRelationsByEntity(string $entity, string $relatedEntity) : array
    {
        return array_filter($this->getEntityRelations($entity), function($relationConfig) use ($relatedEntity)
        {
            return ($relationConfig[0] == 'belongsTo' and $relationConfig[1] == $relatedEntity);
        });
    }

    /**
     * @param string $entity
     * @param string $relatedEntity
     * @param string $field
     * @return array
     */
    public function getBelongsToRelationNameByEntityAndField(string $entity, string $relatedEntity, string $field) : ?string
    {
        $relations = array_filter($this->getEntityRelations($entity), function($relationConfig) use ($relatedEntity, $field)
        {
            return ($relationConfig[0] == 'belongsTo' and $relationConfig[1] == $relatedEntity and $relationConfig[2] == $field);
        });

        return array_keys($relations)[0] ?? null;
    }

    /**
     * @param string $entity
     * @param string|array $type
     * @return array
     */
    public function getRelationsByRelatedEntityAndType(string $entity, string $relatedEntity, $type) : array
    {
        if(!is_array($type))
        {
            $type = [$type];
        }
        return array_filter($this->getEntityRelations($entity), function($relationConfig) use ($relatedEntity, $type)
        {
            return (in_array($relationConfig[0],$type) and $relationConfig[1] == $relatedEntity);
        });
    }

    /**
     * @param string $entity
     * @param string $name
     * @return string|null
     */
    public function getEntityTemplate(string $entity, string $name = 'default') : ?string
    {
        return $this->getStructure()[$entity]['templates'][$name] ?? null;
    }

    /**
     * @param $entity
     * @param $fieldName
     * @return mixed|null
     */
    public function getInverseHasManyRelationName($entity, $fieldName)
    {
        if($belongsToRelation = StructureApi::getBelongsToRelationsByField($entity, $fieldName))
        {
            $belongsTo = reset($belongsToRelation);

            if($inverseRelation = StructureApi::getRelationsByRelatedEntityAndType($belongsTo[1], $entity, 'hasMany'))
            {
                return array_key_first($inverseRelation);
            }
        }

        return null;
    }

    /**
     * @param $entity
     * @return array|null
     */
    public function getBelongsToRelationNames($entity) : ?array
    {
        if($belongsToRelation = StructureApi::getRelationsByType($entity,'belongsTo'))
        {
            return array_keys($belongsToRelation);
        }

        return null;
    }

    /**
     * @param $entity
     * @return array|null
     */
    public function getAttachableBelongsToManyRelationNames($entity) : ?array
    {
        $relations = array_filter($this->getEntityRelations($entity), function($relationConfig)
        {
            return $relationConfig[0] == 'belongsToMany' and (isset($relationConfig[5]) and $relationConfig[5]);
        });

        return array_keys($relations);
    }

    /**
     * @param string $entity
     * @return string
     */
    public function getEntityTitle(string $entity) : ?string
    {
        return $this->getStructure()[$entity]['title'] ?? null;
    }

    /**
     * @param string $entity
     * @return string|null
     */
    public function getEntityPluralTitle(string $entity) : ?string
    {
        return $this->getStructure()[$entity]['title_plural'] ?? null;
    }

    /**
     * @param string $entity
     * @param string $relationName
     * @return string|null
     */
    public function getRelatedEntity(string $entity, string $relationName) : ?string
    {
        if($relation = $this->getRelationByName($entity, $relationName))
        {
            return $relation[1];
        }

        return null;
    }
}