$(function() {

    /*****处理折线图数据*****/
    var infoListLine = $('#infoListLine').html();
    // 执行字符串代码
    eval(infoListLine);
    var res = new Array();
    for(key in listLine){ 
        res[res.length] = {'period':key,'num':listLine[key]};
    }

    /*****处理饼图数据*****/
    var infoListCake = $('#infoListCake').html();
    // 执行字符串代码
    eval(infoListCake);
    var cake = new Array();
    for(key in listCake){ 
        cake[cake.length] = {'label':key,'value':listCake[key]};
    }
    var bar = new Array();
    for(key in listCake){ 
        bar[bar.length] = {'y':key,'a':listCake[key]};
    }
    // 折线图
    Morris.Area({
        element: 'morris-area-chart',
        data:res,
        xkey: 'period',
        // ykeys: ['num', 'ipad', 'itouch'],
        // labels: ['num', 'iPad', 'iPod Touch'],
        ykeys: ['num'],
        labels: ['数目'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true
    });

    // 饼状图
    Morris.Donut({
        element: 'morris-donut-chart',
        data: cake,
        resize: true
    });
    /*
    Morris.Donut({
        element: 'morris-donut-chart',
        data: [{
            label: "张宇",
            value: 56
        }, {
            label: "史海星",
            value: 36
        }, {
            label: "刘志龙",
            value: 68
        }],
        resize: true
    });*/

    // 柱状图
    Morris.Bar({
        element: 'morris-bar-chart',
        data: bar,
        xkey: 'y',
        // ykeys: ['a', 'b'],
        // labels: ['Series A', 'Series B'],
        ykeys: ['a'],
        labels: ['次数 A'],
        hideHover: 'auto',
        resize: true
    });
    /*
    Morris.Bar({
        element: 'morris-bar-chart',
        data: [{
            y: '2015-1',
            a: 100,
            // b: 90
        }, {
            y: '2015-2',
            a: 75,
            // b: 65
        }, {
            y: '2015-3',
            a: 50,
            // b: 40
        }, {
            y: '2015-4',
            a: 75,
            // b: 65
        }, {
            y: '2015-5',
            a: 50,
            // b: 40
        }, {
            y: '2015-6',
            a: 75,
            // b: 65
        }, {
            y: '2015-7',
            a: 100,
            // b: 90
        }],
        xkey: 'y',
        // ykeys: ['a', 'b'],
        // labels: ['Series A', 'Series B'],
        ykeys: ['a'],
        labels: ['次数 A'],
        hideHover: 'auto',
        resize: true
    });*/

});
