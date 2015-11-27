<?php
require_once(BASEDIR . 'includes/node.php');

class Tree
{
    /** @var int $aid */
    private $aid;
    /** @var BlockNode $root */
    private $root;
    /** @var array $scopes */
    private $scopes;

    public function __construct($aid, $tree)
    {
        $this->aid = $aid;
        $this->scopes = [$aid];
        if (is_null($tree)) {
            // if the algorithm is new, generate a BlockNode as root
            $rootNode = BlockNode::parse((object)['i' => 0], $tree, $this->scopes);
        } else {
            // take the tree's last element otherwise
            $rootNode = Node::parse(end($tree), $tree, $this->scopes);
        }
        $this->root = $rootNode;
    }

    public function getRoot()
    {
        return $this->root->getNodeId();
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function printHtml($params)
    {
        $this->root->printHtml($params);
    }

    public function printSource($params)
    {
        // set indent level
        if (!isset($params['indent'])) {
            $params['indent'] = 0;
        }
        // start recursion
        $source = "";
        foreach (explode(PHP_EOL, trim($this->root->getSource($params))) as $line) {
            $source .= '<div class="line">' . $line . '</div>';
        }
        print($source);
    }
}

class ParseError extends Exception
{
}

class ScopeError extends Exception
{
    public function __construct($amount)
    {
        parent::__construct("Cannot load more than $amount of scopes!");
    }
}