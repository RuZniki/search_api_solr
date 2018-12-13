<?php

namespace Drupal\search_api_solr\TypedData;

use Drupal\Core\TypedData\ComplexDataDefinitionBase;
use Drupal\search_api\Entity\Index;

/**
 * A typed data definition class for describing Solr documents.
 */
class SolrDocumentDefinition extends ComplexDataDefinitionBase implements SolrDocumentDefinitionInterface {

  /**
   * The Search API server the Solr document definition belongs to.
   *
   * @var \Drupal\search_api\ServerInterface
   */
  protected $server;

  /**
   * Creates a new Solr document definition.
   *
   * @param \Drupal\search_api\IndexInterface $index
   *   The Search Api index the Solr document definition belongs to.
   *
   * @return static
   */
  public static function create(IndexInterface $index) {
    $definition['type'] = 'solr_document:' . $index->id();
    $document_definition = new static($definition);
    $document_definition->setIndexId($index->id());
    return $document_definition;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromDataType($data_type) {
    // The data type should be in the form of "solr_document:$server_id".
    $parts = explode(':', $data_type, 2);
    if ($parts[0] != 'solr_document') {
      throw new \InvalidArgumentException('Data type must be in the form of "solr_document:INDEX_ID".');
    }
    if (empty($parts[1])) {
      throw new \InvalidArgumentException('A Search API Index must be specified.');
    }

    return self::create(Index::load($parts[1]));
  }

  /**
   * {@inheritdoc}
   */
  public function getIndexId() {
    return isset($this->definition['constraints']['Index']) ? $this->definition['constraints']['Index'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setIndexId(string $index_id) {
    return $this->addConstraint('Index', $index_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $this->propertyDefinitions = [];
      if (!empty($this->getIndexId())) {
        $index = Index::load($this->getIndexId());
        /** @var \Drupal\search_api_solr\SolrFieldManagerInterface $field_manager */
        $field_manager = \Drupal::getContainer()->get('solr_field.manager');
        $this->propertyDefinitions = $field_manager->getFieldDefinitions($index);
      }
    }
    return $this->propertyDefinitions;
  }

}
