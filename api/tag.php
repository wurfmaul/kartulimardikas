<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class TagAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        require_once BASEDIR . 'includes/dataModel.php';
        $_model = new DataModel();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') { // deal with get requests (get tags)
            $this->response['tags'] = $_model->fetchAllTags();

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') { // deal with post requests (edit tags)
            $l10n = $this->loadLanguage($_POST['lang']);
            $tags = trim($_POST['tags']);
            $aid = intval($_POST['aid']);
            // tags before editing
            $current_tags = $_model->fetchTags($aid);
            // tags after editing
            $new_tags = [];
            // tags that are not yet applied
            $tags_to_add = [];
            $this->response['tags'] = "";
            foreach (preg_split('/[\s,;:]+/', $tags) as $tag) {
                if ($tag === "" || in_array($tag, $new_tags)) continue;

                if ($index = array_search($tag, $current_tags)) {
                    // if tag is already applied, mark it, so it won't be deleted
                    unset($current_tags[$index]);
                } elseif (!in_array($tag, $tags_to_add)) {
                    $tags_to_add[] = $tag;
                }
                $new_tags[] = $tag;
                $this->response['tags'] .= "$tag, ";
            }
            $changes = 0;
            if ($current_tags) { // remaining current tags are to remove
                $changes += $_model->deleteTags($current_tags, $aid);
            }
            if ($tags_to_add) {
                $changes += $_model->insertTags($tags_to_add, $aid);
            }
            if ($changes) {
                $this->response['success'] = $l10n['saved_to_db'];
            }
        }

        $_model->close();
        $this->sendResponse();
    }
}

(new TagAPI())->checkAndSend();