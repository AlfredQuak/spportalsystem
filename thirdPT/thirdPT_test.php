<?php

function thirdPT_test($e = null) {
    $s = new SWFShape();
    $f = $s->addFill(0xff, 0, 0);
    $s->setRightFill($f);

    $s->movePenTo(0, 0);
    $s->drawLineTo(100, -100);
    $s->drawLineTo(100, 100);
    $s->drawLineTo(-100, 100);
    $s->drawLineTo(-100, -100);

    $p = new SWFSprite();
    $i = $p->add($s);
    $i->setDepth(1);
    $p->nextFrame();

    for ($n = 0; $n < 5; ++$n) {
        $i->rotate(-15);
        $p->nextFrame();
    }

    $m = new SWFMovie();
    $m->setBackground(0xff, 0xff, 0xff);
    $m->setDimension(200, 200);

    $i = $m->add($p);
    $i->setDepth(1);
    $i->moveTo(0, 20);
    $i->setName("box");

    $m->add(new SWFAction("box.x += 3;"));
    $m->nextFrame();
    $m->add(new SWFAction("gotoFrame(0); play();"));
    $m->nextFrame();
    $m->save("/var/www/test.swf");
    header('Content-type: application/x-shockwave-flash');
    return $m->output();
}

thirdPT_test();
?>
