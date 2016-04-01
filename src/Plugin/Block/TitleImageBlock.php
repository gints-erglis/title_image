<?php
/**
 * Provides a Title Image Block
 *
 * @Block(
 *   id = "title_image_block",
 *   admin_label = @Translation("Title Image Block"),
 * )
 */

namespace Drupal\title_image\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Path\PathMatcherInterface;

class TitleImageBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $current_uri = \Drupal::request()->getRequestUri();
    $content = '';

    $storage = \Drupal::entityManager()->getStorage('node');
    $query = $storage->getQuery();

    $query
    ->condition('status', 1)
    ->condition('type', 'title_image');

    $result = $query->execute();

    $nodes = $storage->loadMultiple($result);

    if (!empty($nodes)) {
      foreach ($nodes as $key => $node) {

        $visibility = $node->get('field_visibility')->getValue();
        $match = \Drupal::service('path.matcher')->matchPath($current_uri, $visibility[0]['value']);
        //$path = current_path();
        //$path_alias = drupal_strtolower(drupal_container()->get('path.alias_manager')->getPathAlias($path));
        if($match) {
          $title_node = $node;
          break;
        }
      }
    }

    if(!empty($title_node)){
      $content = drupal_render(node_view($title_node));
    }


    $build = [];
    $build['#markup'] = $content;
    $build['#cache'] = [ 'contexts' => [ 'url.path' ] ];

    return $build;
  }

}
?>
