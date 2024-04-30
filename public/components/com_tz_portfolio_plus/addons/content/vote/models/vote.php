<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusContentVoteModelVote extends TZ_Portfolio_PlusPluginModelItem {

    public function getItem($pk = null)
    {

        if($item = parent::getItem($pk)) {
            if(isset($item -> id)){
                $item -> rating_count   = 0;
                $item -> rating_sum     = 0;

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);

                $query->select('*');
                $query->from('#__tz_portfolio_plus_content_rating');
                $query->where('content_id = ' . (int) $item -> id);
                $db->setQuery($query);

                if($vote = $db -> loadObject()) {
                    foreach($vote as $key => $value){
                        $item -> $key   = $value;
                    }
                }
            }
            return $item;
        }
        return false;
    }

    public function save($data){

        $cid            = $data['cid'];
        $user_rating    = $data['user_rating'];

        $db     = $this -> getDbo();
        $query	= $db -> getQuery(true);

        $input  = JFactory::getApplication() -> input;

        try{
            // Get ip
            $currip = $input -> server -> get('REMOTE_ADDR', '', 'string');

            if($votesdb = TZ_Portfolio_PlusAddOnContentVoteHelper::getVoteByArticleId($cid)){
                $query -> clear();
                $query -> update('#__tz_portfolio_plus_content_rating');
                $query -> set('rating_count = rating_count + 1')
                    -> set('rating_sum = rating_sum + ' .   $user_rating)
                    -> set('lastip='.$db -> quote($currip));
                $query -> where('content_id = '. $cid);
            }else{
                // Insert voting
                $query -> clear();
                $query -> insert('#__tz_portfolio_plus_content_rating');
                $query -> columns('content_id, lastip, rating_sum, rating_count');
                $query -> values($cid.','. $db -> quote($currip).','.$user_rating.',1');
            }

            $db -> setQuery( $query );
            $db -> execute();
        }catch (Exception $exception){
            $this -> setError($exception -> getMessage());
            return false;
        }
        return true;
    }

    public function delete($table){

        if(!$table || (isset($table -> id) && !$table -> id)){
            return false;
        }

        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> delete('#__tz_portfolio_plus_content_rating');
        $query -> where('content_id='.$table -> id);
        $db -> setQuery($query);
        return $db -> execute();
    }
}