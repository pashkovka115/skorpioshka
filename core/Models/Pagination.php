<?php
namespace Core\Models;

// https://www.codexworld.com/php-pagination-class-with-mysql/
// https://www.youtube.com/watch?v=LG-kkuFHsqg

/*
$baseURL – URL веб-страницы.
$totalRows – Общее количество предметов.
$perPage – Количество записей, которые нужно отобразить на каждой странице.
$numLinks – Количество ссылок для показа.
$firstLink – Первая ссылка на ярлык.
$nextLink – Следующая ссылка ярлык.
$prevLink – Предыдущая ссылка на ярлык.
$lastLink – Последняя ссылка.
$fullTagOpen – Полный открытый тег.
$fullTagClose – Полный закрывающий тег.
$firstTagOpen – Первый открытый тег.
$firstTagClose – Первый закрывающий тег.
$lastTagOpen – Последний открытый тег.
$lastTagClose – Последний закрывающий тег.
$curTagOpen – Текущий открытый тег.
$curTagClose – Текущий закрывающий тег.
$nextTagOpen – Следующий открытый тег.
$nextTagClose – Следующий закрывающий тег.
$prevTagOpen – Предыдущий открытый тег.
$prevTagClose – Предыдущий закрывающий тег.
$numTagOpen – Номер открытого тега.
$numTagClose – Номер закрывающий тег.
$showCount – Показать количество ссылок.
$queryStringSegment – Флаг строки запроса страницы.
 */


/**
 * CodexWorld
 *
 * This Pagination class helps to integrate pagination in PHP.
 *
 * @class      Pagination
 * @author     CodexWorld
 * @link       http://www.codexworld.com
 * @license    http://www.codexworld.com/license
 * @version    2.0
 */
class Pagination
{
    protected $baseURL            = '';
    protected $totalRows          = '';
    protected $perPage            = 10;
    protected $numLinks           = 2;
    protected $currentPage        = 0;
    protected $firstLink          = 'Первая';
    protected $nextLink           = 'Следующая &raquo;';
    protected $prevLink           = '&laquo; Предыдущая';
    protected $lastLink           = 'Последняя';
    protected $fullTagOpen        = '<div class="pagination">';
    protected $fullTagClose       = '</div>';
    protected $firstTagOpen       = '';
    protected $firstTagClose      = '&nbsp;';
    protected $lastTagOpen        = '&nbsp;';
    protected $lastTagClose       = '';
    protected $curTagOpen         = '&nbsp;<b>';
    protected $curTagClose        = '</b>';
    protected $nextTagOpen        = '&nbsp;';
    protected $nextTagClose       = '&nbsp;';
    protected $prevTagOpen        = '&nbsp;';
    protected $prevTagClose       = '';
    protected $numTagOpen         = '&nbsp;';
    protected $numTagClose        = '';
    protected $showCount          = true;
    protected $currentOffset      = 0;
    protected $queryStringSegment = 'page';

    protected $data;

    public function __construct($totalRows, $data, $params = [])
    {
        $this->baseURL = explode('?',$_SERVER['REQUEST_SCHEME'] .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])[0];
        $this->data = $data;
        $this->totalRows = $totalRows;

        if (count($params) > 0) {
            $this->initialize($params);
        }

//        dump($data);
        return $this;
    }

    public function initialize($params = [])
    {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * Generate the pagination links
     */
    public function createLinks()
    {
        // Если общее количество строк равно нулю, продолжать не нужно
        if ($this->totalRows == 0 or $this->perPage == 0) {
            return '';
        }
        // Рассчитать общее количество страниц
        $numPages = ceil($this->totalRows / $this->perPage);
        // Есть только одна страница? не нужно будет продолжать
        if ($numPages == 1) {
            if ($this->showCount) {
                $info = 'Показано : ' . $this->totalRows;
                return $info;
            } else {
                return '';
            }
        }

        // Определить строку запроса
        $query_string_sep = (strpos($this->baseURL, '?') === FALSE) ? '?page=' : '&amp;page=';
        $this->baseURL = $this->baseURL . $query_string_sep;

        // Определить текущую страницу
        $this->currentPage = isset($_GET[$this->queryStringSegment]) ? $_GET[$this->queryStringSegment] : 0;

        if (!is_numeric($this->currentPage) || $this->currentPage == 0) {
            $this->currentPage = 1;
        }

        // Переменная содержимого ссылки
        $output = '';

        // Отображение ссылок на уведомления
        if ($this->showCount) {
            $currentOffset = ($this->currentPage > 1) ? ($this->currentPage - 1) * $this->perPage : $this->currentPage;
            $info = 'Показано с ' . $currentOffset . ' до ';

            if (($currentOffset + $this->perPage) <= $this->totalRows)
                $info .= $this->currentPage * $this->perPage;
            else
                $info .= $this->totalRows;

            $info .= ' из ' . $this->totalRows . ' | ';

            $output .= $info;
        }

        $this->numLinks = (int)$this->numLinks;

        // Номер страницы выходит за пределы диапазона результатов? последняя страница покажет
        if ($this->currentPage > $this->totalRows) {
            $this->currentPage = $numPages;
        }

        $uriPageNum = $this->currentPage;

        // Рассчитайте начальные и конечные числа.
        $start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
        $end = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

        // Рендер "Первая" ссылка
        if ($this->currentPage > $this->numLinks) {
            $firstPageURL = str_replace($query_string_sep, '', $this->baseURL);
            $output .= $this->firstTagOpen . '<a href="' . $firstPageURL . '">' . $this->firstLink . '</a>' . $this->firstTagClose;
        }
        // Визуализировать «предыдущую» ссылку
        if ($this->currentPage != 1) {
            $i = ($uriPageNum - 1);
            if ($i == 0) $i = '';
            $output .= $this->prevTagOpen . '<a href="' . $this->baseURL . $i . '">' . $this->prevLink . '</a>' . $this->prevTagClose;
        }
        // Напишите цифровые ссылки
        for ($loop = $start - 1; $loop <= $end; $loop++) {
            $i = $loop;
            if ($i >= 1) {
                if ($this->currentPage == $loop) {
                    $output .= $this->curTagOpen . $loop . $this->curTagClose;
                } else {
                    $output .= $this->numTagOpen . '<a href="' . $this->baseURL . $i . '">' . $loop . '</a>' . $this->numTagClose;
                }
            }
        }
        // Визуализировать ссылку «Следующая»
        if ($this->currentPage < $numPages) {
            $i = ($this->currentPage + 1);
            $output .= $this->nextTagOpen . '<a href="' . $this->baseURL . $i . '">' . $this->nextLink . '</a>' . $this->nextTagClose;
        }
        // Рендеринг "Последняя" ссылка
        if (($this->currentPage + $this->numLinks) < $numPages) {
            $i = $numPages;
            $output .= $this->lastTagOpen . '<a href="' . $this->baseURL . $i . '">' . $this->lastLink . '</a>' . $this->lastTagClose;
        }
        // Удалить двойную косую черту
        $output = preg_replace("#([^:])//+#", "\\1/", $output);
        // Добавьте обертку HTML, если существует
        $output = $this->fullTagOpen . $output . $this->fullTagClose;

        return $output;
    }


    public function get()
    {
        return $this->data;
    }
}
