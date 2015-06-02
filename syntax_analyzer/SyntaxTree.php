<?php

class Node {
    public $symbol;
    public $value;
    public $children = [];

    public function __construct($symbol, $value) {
        $this->symbol = $symbol;
        $this->value = $value;
    }
}

class SyntaxTree {
    public $topTreeLevel = [];
    public $rules = [];
    public $iteration = 0;

    public function __construct($rules) {
        $this->rules = $rules;
    }

    public function giveToken($token) {
        $this->topTreeLevel[] = new Node($token->type, $token->text);
        $this->wrapTree();
//        $this->logTopLevel();
    }

    private function wrapTree() {
        $wrapMore = false;
        for($i=1;$i<=count($this->topTreeLevel);$i++) {
            if ($this->wrapNodes(array_slice($this->topTreeLevel, -$i), $i)) {
                $wrapMore = true;
                break;
            }
        }
        if ($wrapMore) return $this->wrapTree();
        return true;
    }

    private function wrapNodes($nodes = [], $i) {
//        $this->logNodes($nodes);
        $nodesRule = [];
        foreach($nodes as $node) {
            $nodesRule[] = $node->symbol;
        }
        foreach($this->rules as $production) {
            if ($production['rule'] == $nodesRule) {
                $this->topTreeLevel = array_slice($this->topTreeLevel, 0, count($this->topTreeLevel)-$i);
                $wrapNode = new Node($production['non-terminal'], '');
                foreach($nodes as $node) $wrapNode->children[] = &$node;
                $this->topTreeLevel[] = $wrapNode;
                return true;
            }
        }
        return false;
    }

    private function logNodes($nodes = []) {
        echo '---------------------'.PHP_EOL;
        echo 'Wrap iteration #'.$this->iteration.':'.PHP_EOL;
        foreach ($nodes as $node) echo $node->symbol.PHP_EOL;
        $this->iteration++;
    }

    private function logTopLevel() {
        echo '---------------------'.PHP_EOL;
        echo 'Wrap iteration #'.$this->iteration.':'.PHP_EOL;
        foreach ($this->topTreeLevel as $item) echo $item->symbol.PHP_EOL;
        $this->iteration++;
    }
}