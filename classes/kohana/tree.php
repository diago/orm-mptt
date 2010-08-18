<?php

/**
 * Modified Preorder Tree Traversal Helpers.
 * 
 *
 * @author diago
 * @copyright (c) 2010 Outland, LLC
 * @package ORM_MPTT
 * @license http://github.com/diago/licenses/raw/master/mit.txt
 */

class Kohana_Tree
{
  
  /**
   * Builds a ul or ol tree list from an ORM_MPTT object.
   *
   * The view should be the start of the li only; NO CLOSING TAG!
   * The variable 'record' will be passed into the view for use.
   *
   * @param Database_Result Usually from ORM::factory('table')->fulltree();
   * @param View|string     Either the view or view path
   * @param boolean         Suppresses root node by default
   * @param string          type of list to create
   * @return string
   **/
  public static function builder($records, $view, $show_root=FALSE, $type='ul')
  {
    if( ! $view instanceOf View)
    {
      $view = new View($view);
    }

    // quick sanitize
    $type = strtolower($type);

    // grab the first record 
    $current = is_object($records) ? 
               $records->current() :
               current($records);

    // grab the user defined column names
    $cols = Kohana_Tree::get_column_names($current);

    $open = '<'.$type.'>';
    $close= '</'.$type.'>';

    $built = $open;

    $start = $level = $current->$cols['level'];

    // clean up
    unset($current);

    $first = TRUE;

    foreach($records AS $r)
    {
  
      if( ! $show_root AND $r->is_root())
      {
        $start = $level++;
        continue;
      }

      // if the level has increased start a new list
      if($r->$cols['level'] > $level)
      {
        $built .= $open;
      }
      else if($r->$cols['level'] < $level) // close the list
      {
        // figure out how many levels we went down
        $built .= str_repeat($close.'</li>', ($level - $r->$cols['level']));
      }
      else if( ! $first)
      {
        $built .= '</li>';
      }

      $built .= $view->set('record', $r);

      $level = $r->$cols['level'];

      $first = FALSE;

    }

    // close the list
    $built .= str_repeat('</li>'.$close, ($level - $start) + 1);

    return $built;
  }


  /**
   * Returns an array of the defined column names: left, right, scope, level, parent
   *
   * @param ORM_MPTT record
   * @return array
   **/
  public static function get_column_names($record)
  {

    $cols = array();
    $cols['left'] = $record->left_column;
    $cols['right'] = $record->right_column;
    $cols['level'] = $record->level_column;
    $cols['scope'] = $record->scope_column;
    $cols['parent'] = $record->parent_column;

    unset($record);

    return $cols;

  }

}
