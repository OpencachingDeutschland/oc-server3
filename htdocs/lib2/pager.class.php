<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Set template variables for displaying a page browser control.
 *  Output is formatted by templates2/<style>/res_pager.tpl.
 ***************************************************************************/

class pager
{
    private $link_url;
    private $min_pages_shown;
    private $max_pages_shown;


    // Use {page} in link_url als placeholder for the selected page number
    // and/or {offset} for the 0-based data offset of the selected page.

    public function __construct($link_url, $min_pages_shown = 2, $max_pages_shown = 15)
    {
        global $tpl;

        $this->link_url = $link_url;
        $this->min_pages_shown = $min_pages_shown;
        if (($max_pages_shown % 2) == 0) {
            $tpl->error("pager: max pages shown must be odd");
        }
        $this->max_pages_shown = $max_pages_shown;
    }


    public function make_from_pagenr($current_page, $total_pages, $page_size = false)
    {
        global $tpl;

        if (mb_strpos($this->link_url, '{offset}') && $page_size === false) {
            $tpl->error('page size is not set for offset paging');
        } elseif ($total_pages < $this->min_pages_shown) {
            // not enough pages - disable pager
            $tpl->assign('pages_list', false);
        } else {
            $first_page = 1;
            $last_page = $total_pages;
            $current_page = min(max($current_page, $first_page), $last_page);

            if ($current_page == $first_page) {
                $tpl->assign('pages_first_link', false);
                $tpl->assign('pages_prev_link', false);
            } else {
                $tpl->assign('pages_first_link', $this->pagelink($first_page, $page_size));
                $tpl->assign('pages_prev_link', $this->pagelink($current_page - 1, $page_size));
            }

            $pages = array();
            $lrspan = ($this->max_pages_shown - 1) / 2;
            $from_page = max($first_page, $current_page - $lrspan);
            $to_page = min($last_page, max($first_page, $current_page - $lrspan) + $this->max_pages_shown - 1);
            $from_page = max($first_page, $to_page - $this->max_pages_shown + 1);

            for ($page = $from_page; $page <= $to_page; $page ++) {
                if ($page == $current_page) {
                    $pages[$page] = false;
                } else {
                    $pages[$page] = $this->pagelink($page, $page_size);
                }
            }
            $tpl->assign('pages_list', $pages);

            if ($current_page == $last_page) {
                $tpl->assign('pages_next_link', false);
                $tpl->assign('pages_last_link', false);
            } else {
                $tpl->assign('pages_next_link', $this->pagelink($current_page + 1, $page_size));
                $tpl->assign('pages_last_link', $this->pagelink($last_page, $page_size));
            }

            if ($last_page - $first_page < 2) {
                $tpl->assign('pages_first_link', null);
                $tpl->assign('pages_last_link', null);
            }
        }
    }


    public function make_from_offset($current_offset, $total_items, $page_size)
    {
        $this->make_from_pagenr(
            floor($current_offset / $page_size) + 1,
            ceil($total_items / $page_size),
            $page_size
        );
    }


    private function pagelink($page, $page_size)
    {
        return mb_ereg_replace(
            '{page}',
            $page,
            mb_ereg_replace(
                '{offset}',
                ($page - 1) * $page_size,
                $this->link_url
            )
        );
    }
}
