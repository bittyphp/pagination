<?php
use Kijtra\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructOnlyTotal()
    {
        $total = 100;
        $page = new Pagination($total);
        $info = $page->getInfo();
        $this->assertSame($info->total, $total);
        $this->assertSame($info->current, 1);
    }

    public function testConstructTotalCurrent()
    {
        $total = 100;
        $page = new Pagination($total, 2);
        $info = $page->getInfo();
        $this->assertSame($info->total, $total);
        $this->assertSame($info->current, 2);
    }

    public function testConstructArray()
    {
        $total = 100;
        $page = new Pagination(array(
            'total' => $total,
            'current' => 3,
        ));
        $info = $page->getInfo();
        $this->assertSame($info->total, $total);
        $this->assertSame($info->current, 3);
    }

    public function testSetDefaultLimit()
    {
        $defaults = Pagination::getDefaults();
        Pagination::setDefaults('limit', 22);
        $page = new Pagination();
        $info = $page->getInfo();
        Pagination::setDefaults('limit', $defaults['limit']);
        $this->assertSame($info->limit, 22);
    }

    public function testSetDefaultArray()
    {
        Pagination::setDefaults(array(
            'limit' => 22,
        ));
        $page = new Pagination();
        $info = $page->getInfo();
        $this->assertSame($info->limit, 22);
    }

    public function testSetRender()
    {
        $page = new Pagination();
        $page->setRender(function () {
            return;
        });
    }

    public function testSetRenderException()
    {
        try {
            $page = new Pagination();
            $page->setRender('not callable');
            $this->assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    public function testSetOptions()
    {
        $total = 100;
        $page = new Pagination();
        $page->setOptions(array(
            'total' => $total,
            'current' => 3,
        ));
        $info = $page->getInfo();
        $this->assertSame($info->total, $total);
        $this->assertSame($info->current, 3);
    }

    public function testSetOptionsException()
    {
        try {
            $page = new Pagination();
            $page->setOptions('not array');
            $this->assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    public function testSetTotal()
    {
        $n = 100;
        $page = new Pagination();
        $page->setTotal($n);
        $info = $page->getInfo();
        $this->assertSame($info->total, $n);
    }

    public function testSetCurrent()
    {
        $n = 100;
        $page = new Pagination();
        $page->setCurrent($n);
        $info = $page->getInfo();
        $this->assertSame($info->current, $n);
    }

    public function testSetLimit()
    {
        $n = 100;
        $page = new Pagination();
        $page->setLimit($n);
        $info = $page->getInfo();
        $this->assertSame($info->limit, $n);

        $defaults = Pagination::getDefaults();
        $page = new Pagination();
        $page->setLimit('a');
        $info = $page->getInfo();
        $this->assertSame($info->limit, $defaults['limit']);
    }

    public function testSetRange()
    {
        $n = 100;
        $page = new Pagination();
        $page->setRange($n);
        $info = $page->getInfo();
        $this->assertSame($info->range, $n);

        $defaults = Pagination::getDefaults();
        $page = new Pagination();
        $page->setRange('a');
        $info = $page->getInfo();
        $this->assertSame($info->range, $defaults['range']);
    }

    public function testCalc()
    {
        $opt = array(
            'total' => 100,
            'current' => 2,
            'limit' => 25,
        );
        $page = new Pagination($opt);
        $info = $page->getInfo();

        $this->assertSame($info->pages, 4);
        $this->assertSame($info->from, 25);
        $this->assertSame($info->to, 50);
        $this->assertSame($info->end, 4);
    }

    public function testRender()
    {
        $opt = array(
            'total' => 100,
            'current' => 2,
            'limit' => 25,
        );
        $page = new Pagination($opt);
        $page->setRender(function ($info) {
            $arr = array();
            $arr[] = 'prev:'.$info->prev;
            $arr[] = 'next:'.$info->next;
            $arr[] = 'start:'.$info->start;
            $arr[] = 'end:'.$info->end;
            return implode(',', $arr);
        });

        $info = $page->getInfo();
        $arr = array();
        $arr[] = 'prev:'.$info->prev;
        $arr[] = 'next:'.$info->next;
        $arr[] = 'start:'.$info->start;
        $arr[] = 'end:'.$info->end;

        $this->assertSame($page->render(), implode(',', $arr));
    }
}
