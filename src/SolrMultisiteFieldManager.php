<?php

namespace Drupal\search_api_solr;

use Drupal\search_api\IndexInterface;
use Drupal\search_api_solr\TypedData\SolrMultisiteFieldDefinition;

/**
 * Manages the discovery of Solr fields.
 */
class SolrMultisiteFieldManager extends SolrFieldManager {

  /**
   * Builds the field definitions for a multisite index.
   *
   * @param \Drupal\search_api\IndexInterface $index
   *   The index from which we are retrieving field information.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   The array of field definitions for the server, keyed by field name.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  protected function buildFieldDefinitions(IndexInterface $index) {
    $fields = [];
    foreach ($index->getFields() as $index_field) {
      $field = new SolrMultisiteFieldDefinition();
      $field->setLabel($index_field->getLabel());
      $field->setDataType($index_field->getType());
      $fields[$index_field->getPropertyPath()] = $field;
    }
    return $fields;
  }

}
