<?php
//计算向量叉乘
function crossMul($v1, $v2)
{
    return $v1['x'] * $v2['y'] - $v1['y'] * $v2['x'];
}

//判断两条线段是否相交
function checkCross($p1, $p2, $p3, $p4)
{
    $v1 = array('x' => $p1['x'] - $p3['x'], 'y' => $p1['y'] - $p3['y']);
    $v2 = array('x' => $p2['x'] - $p3['x'], 'y' => $p2['y'] - $p3['y']);
    $v3 = array('x' => $p4['x'] - $p3['x'], 'y' => $p4['y'] - $p3['y']);
    $v = crossMul($v1, $v3) * crossMul($v2, $v3);
    $v1 = array('x' => $p3['x'] - $p1['x'], 'y' => $p3['y'] - $p1['y']);
    $v2 = array('x' => $p4['x'] - $p1['x'], 'y' => $p4['y'] - $p1['y']);
    $v3 = array('x' => $p2['x'] - $p1['x'], 'y' => $p2['y'] - $p1['y']);
    return ($v <= 0 && crossMul($v1, $v3) * crossMul($v2, $v3) <= 0) ? true : false;
}

//判断点是否在多边形内
function checkPP($point, $polygon)
{
    $p1 = $point;
    $p2 = array('x' => -100, 'y' => $point['y']);
    $count = 0;
    $polygon_length = count($polygon);
    //对每条边都和射线作对比
    for ($i = 0; $i < $polygon_length - 1; $i++) {
        $p3 = $polygon[$i];
        $p4 = $polygon[$i + 1];
        if (checkCross($p1, $p2, $p3, $p4) == true) {
            $count++;
        }
    }
    $p3 = $polygon[$polygon_length - 1];
    $p4 = $polygon[0];
    if (checkCross($p1, $p2, $p3, $p4) == true) {
        $count++;
    }
    return ($count % 2 == 0) ? false : true;
}

$point = array('x' => 81.35, 'y' => 121.29);
$polygon = array(
    array('x' => 81.64, 'y' => 120.90),
    array('x' => 81.14, 'y' => 121.02),
    array('x' => 80.91, 'y' => 120.79),
    array('x' => 80.60, 'y' => 121.15),
    array('x' => 81.54, 'y' => 122.02),
    array('x' => 81.88, 'y' => 121.30),
    array('x' => 81.54, 'y' => 121.99),
);
var_dump(checkPP($point, $polygon));
?>
<script>
    //计算向量叉乘
    var crossMul = function (v1, v2) {
        return   v1.x * v2['y'] - v1.y * v2.x;
    };
    //判断两条线段是否相交
    var checkCross = function (p1, p2, p3, p4) {
        var v1 = {x:p1.x - p3.x, y:p1.y - p3.y},
                v2 = {x:p2.x - p3.x, y:p2.y - p3.y},
                v3 = {x:p4.x - p3.x, y:p4.y - p3.y},
                v = crossMul(v1, v3) * crossMul(v2, v3);
        v1 = {x:p3.x - p1.x, y:p3.y - p1.y};
        v2 = {x:p4.x - p1.x, y:p4.y - p1.y};
        v3 = {x:p2.x - p1.x, y:p2.y - p1.y};
        return (v <= 0 && crossMul(v1, v3) * crossMul(v2, v3) <= 0) ? true : false;
    };
    //判断点是否在多边形内
    var checkPP = function (point, polygon) {
        var p1, p2, p3, p4;
        p1 = point;
        p2 = {x:-100, y:point.y};
        var count = 0
        //对每条边都和射线作对比
        for (var i = 0; i < polygon.length - 1; i++) {
            p3 = polygon[i];
            p4 = polygon[i + 1];
            if (checkCross(p1, p2, p3, p4) == true) {
                count++
            }
        }
        p3 = polygon[polygon.length - 1];
        p4 = polygon[0];
        if (checkCross(p1, p2, p3, p4) == true) {
            count++
        }
        //  console.log(count)
        return (count % 2 == 0) ? false : true;
    };
    var point = {x:81.35, y:121.29};
    var polygon = [
        {x:81.64, y:120.90},
        {x:81.14, y:121.02},
        {x:80.91, y:120.79},
        {x:80.60, y:121.15},
        {x:81.54, y:122.02},
        {x:81.88, y:121.30},
        {x:81.54, y:121.99},
    ];
    var ok = checkPP(point, polygon);
    alert(ok);
</script>