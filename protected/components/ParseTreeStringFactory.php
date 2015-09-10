<?php

/**
 * Description of ParseTreeStringFactory
 * @author Andry Luthfi
 */
trait ParseTreeStringFactory {

    /**
     * Parse the string and collects all leaf nodes' string
     * @return string a whole sentence built by collection all leaf nodes' 
     *                string 
     */
    public function getSentence() {
        $matches = [];
        $words = [];
        if (preg_match_all('/\(([^()]+)\)/', $this->string, $matches)) {
            if (isset($matches[1])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $words[] = $matches[1][$i];
                }
            }
        }
        return implode(' ', $words);
    }

}
