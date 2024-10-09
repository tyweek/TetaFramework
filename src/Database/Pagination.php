<?php

namespace TetaFramework\Database;

class Pagination
{
    protected $totalItems;
    protected $itemsPerPage;
    protected $currentPage;

    public function __construct($totalItems, $itemsPerPage, $currentPage)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
    }

    public function getStartPage()
    {
        return max($this->currentPage - 2, 1);
    }
    public function getEndPage()
    {
        return min($this->currentPage + 2, $this->getTotalPages());
    }
    public function getTotalPages()
    {
        return ceil($this->totalItems / $this->itemsPerPage);
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage()
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    public function hasNextPage()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getNextPage()
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    public function render()
    {
        $html = '<nav aria-label="Page navigation">';
        $html .= '<ul class="pagination justify-content-center">';

        if ($this->hasPreviousPage()) {
            $html .= '<li class="page-item"><a class="page-link" href="?page=' . $this->getPreviousPage() . '">Previous</a></li>';
        }

        for ($i = 1; $i <= $this->getTotalPages(); $i++) {
            $html .= '<li class="page-item ' . ($this->getCurrentPage() == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }

        if ($this->hasNextPage()) {
            $html .= '<li class="page-item"><a class="page-link" href="?page=' . $this->getNextPage() . '">Next</a></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }
}
