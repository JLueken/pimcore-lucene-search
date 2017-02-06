<?php

namespace LuceneSearch\Helper\View;

class SearchHelper extends \Zend_View_Helper_Abstract
{
    /**
     * @return $this
     */
    public function searchHelper()
    {
        return $this;
    }

    /**
     * @param array $customParams
     */
    public function getPagination($customParams = [])
    {
        $defaults = [
            'paginationUrl'      => '',
            'paginationElements' => 5,
            'viewTemplate'       => 'default',
            'paginationClass'    => 'paginator'
        ];

        $params = array_merge($defaults, $customParams);

        $pageStart = 1;
        $searchCurrentPage = (int)$this->view->searchCurrentPage;
        $searchAllPages = (int)$this->view->searchAllPages;

        if ($searchCurrentPage > ceil($params['paginationElements'] / 2)) {
            $pageStart = $searchCurrentPage - 2;
        }

        $pageEnd = $pageStart + $params['paginationElements'];

        if ($pageEnd > $searchAllPages) {
            $pageEnd = $searchAllPages;
        }

        $paginationUrlInfo = parse_url($params['paginationUrl']);

        $path = '';
        $scheme = '';
        $host = '';

        if (isset($paginationUrlInfo['query']) && !empty($paginationUrlInfo['query'])) {
            $q = $paginationUrlInfo['query'];
            $paginationUrl = '?' . $q . (substr($q, -1) === '&' ? '' : '&');
        } else {
            $paginationUrl = '?';
        }

        if (isset($paginationUrlInfo['path']) && !empty($paginationUrlInfo['path'])) {
            $path = $paginationUrlInfo['path'];
        }

        if (isset($paginationUrlInfo['scheme']) && !empty($paginationUrlInfo['scheme'])) {
            $scheme = $paginationUrlInfo['scheme'] . '://';
        }

        if (isset($paginationUrlInfo['host']) && !empty($paginationUrlInfo['host'])) {
            $host = $paginationUrlInfo['host'];
        }

        $viewParams = [
            'searchUrl'         => $scheme . $host . $path . $paginationUrl,
            'currentSearchPage' => $searchCurrentPage,
            'searchAllPages'    => $searchAllPages,
            'searchPageStart'   => $pageStart,
            'searchPageEnd'     => $pageEnd,
            'searchUrlData'     => $this->createPaginationUrl(),
            'class'             => $params['paginationClass']
        ];

        return $this->view->partial('/search/helper/pagination/' . $params['viewTemplate'] . '.php', $viewParams);
    }

    /**
     * @param string $query
     * @param bool   $returnAsArray
     *
     * @return array|string
     */
    public function createPaginationUrl($query = '', $returnAsArray = FALSE)
    {
        $params = [
            'language' => !empty($this->view->searchLanguage) ? $this->view->searchLanguage : NULL,
            'country'  => !empty($this->view->searchCountry) ? $this->view->searchCountry : NULL,
            'category' => !empty($this->view->searchCategory) ? $this->view->searchCategory : NULL,
            'q'        => !empty($query) ? $query : $this->view->searchQuery
        ];

        return $returnAsArray ? $params : http_build_query($params);
    }
}