<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title id="page-title">VaSOn Annotation Button</title>
    <link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css">
    <script type="text/javascript" src="extjs/ext-all.js"></script>

<body>

<h1 style="font-size: 200%">Welcome to the VaSOn Annotation Button app!</h1>
<br /><br />

<?php
$base_url = sprintf("http%s://%s", isset($_SERVER['HTTPS']) ? "s" : "",  $_SERVER['SERVER_NAME']);
?>

<span style="font-size: 150%">
To begin, please drag this
<a style="padding: 5px; text-decoration: none; border: 1px dashed blue" 
   href="javascript:(function(){window.open('<?=$base_url?>/vason/button.php?url='+encodeURIComponent(location.href)+'&text='+encodeURIComponent(getSelection().toString())+'&_id='+Math.random(),'_blank','width=500,height=150')})()">
  VaSOn Button</a>
in your browser&apos;s bookmark area or toolbar.
<br /><br />

To use, first select some text (i.e. highlight it with the mouse) that you wish to annotate in the page you are currently browsing, then click the button, and complete the annotation in the popup window. 
The annotation items are grouped by URL/text, so you can correct or delete a previous entry by selecting it again.
You can check the content of the database in real-time, with the grid below.
<br /><br />

Be aware that as the JS framework it&apos;s based upon (<a href="http://www.sencha.com/products/extjs/">Ext JS</a>) is quite heavy, 
it may take a little while the first time you load it (after that it should remain cached by your browser). I only tested it with Chrome and Firefox,
but it should work with any modern browser.
<br /><br />

The code is available on <a href="https://github.com/cjauvin/vason">GitHub</a>.
<br /><br />

Todo: add/restructure fields if needed, add autocompletion, make grid items deletable.
<br /><br />
</span>

<script type="text/javascript">

Ext.onReady(function() {

    Ext.define('Doc', {
        extend: 'Ext.data.Model',        
        fields: ['url', 'text', 'annotation', {name:'time', type:'date', dateFormat: 'c'}, 'user']
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'Doc',
        autoLoad: true,
        groupField: 'url',
        proxy: {
            type: 'ajax',
            url: '/vason_server/list',
            reader: {
                type: 'json',
                root: 'rows'
            }
        }            
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: '{name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
        hideGroupedHeader: true
    });

    var grid = Ext.create('Ext.grid.Panel', {
        renderTo: document.body,
        title: 'Saved Annotations',
        store: store,
        width: 800,
        height: 400,
        features: [groupingFeature],
        resizable: true,
        columns: [{
            header: 'URL', 
            dataIndex: 'url',
            sortable: false
        }, {
            header: 'Text', 
            dataIndex: 'text', 
            flex: 2,
            sortable: false
        }, {
            header: 'Annotation', 
            dataIndex: 'annotation', 
            flex: 2,
            sortable: false            
        }, {
            header: 'User', 
            dataIndex: 'user',
            sortable: false,
            flex: 1
        }, {
            header: 'Time', 
            dataIndex: 'time',
            sortable: false,
            flex: 1,
            renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')
        }],
        dockedItems: {
            xtype: 'pagingtoolbar',
            store: store,
            dock: 'bottom',
            displayInfo: true
        }
    });

});
</script>

</body>
</html>
