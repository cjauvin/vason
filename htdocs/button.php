<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title id="page-title">VaSOn Annotation Button</title>
    <link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css">
    <script type="text/javascript" src="extjs/ext-all.js"></script>

<script type="text/javascript">

<?php 
echo sprintf("var url = '%s';", get_magic_quotes_gpc() ? $_GET['url'] : addslashes($_GET['url']));
echo sprintf("var text = '%s';", get_magic_quotes_gpc() ? $_GET['text'] : addslashes($_GET['text'])); 
?>

Ext.onReady(function() {

    var process = function(action) {
        if (Ext.getCmp('url').getValue() == '[not available: please supply it]') {
            Ext.getCmp('url').markInvalid('URL not available');
            return;
        }
        if (Ext.getCmp('text').getValue() == '[not available: please supply it]') {
            Ext.getCmp('text').markInvalid('text not available');
            return;
        }
        if (form.getForm().isValid()) {
            form.getForm().submit({
                url: '/vason_server/' + action,
                success: function(_form, action) {
                    window.close();
                },
                failure: function(_form, action) {
                    document.write('<b>Sorry there was an error.. please send the following to cjauvin@gmail.com:</b><br/><br />' + action.result.error_msg);
                }
            });
        }        
    };

    var form = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: true,
        width: 500,
        height: 150,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 65 
       },
        defaultType: 'textfield',
        items: [{
            xtype: 'combo',
            fieldLabel: 'User',
            name: 'user',
            allowBlank: false,
            editable: false,
            triggerAction: 'all',
            typeAhead: false,
            mode: 'local',
            store: ['Stephanie', 'Christian', 'Arash', 'David']
        }, {
            fieldLabel: 'URL',
            name: 'url',
            id: 'url',
            value: url ? url : '[not available: please supply it]',
            allowBlank: false
        }, {
            fieldLabel: 'Text',
            name: 'text',
            id: 'text',
            value: text ? text : '[not available: please supply it]',
            allowBlank: false
        }, {
            fieldLabel: 'Annotation',
            name: 'annotation',
            allowBlank: false
        }],
        loader: {
            url: '/vason_server/retrieve',
            renderer: function(loader, resp, opts) {
                var result = Ext.JSON.decode(resp.responseText);
                if (result.hasOwnProperty('error_msg')) {
                    document.write('<b>Sorry there was an error.. please send the following to cjauvin@gmail.com:</b><br/><br />' + result.error_msg);
                    return;
                }
                if (result) {
                    form.getForm().loadRecord(result);
                    if (result.hasOwnProperty('warning_msg')) {
                        Ext.Msg.show({
                            title: 'Warning', 
                            msg: result.warning_msg,
                            icon: Ext.MessageBox.WARNING,
                            buttons: Ext.MessageBox.OK
                        });                                
                    }
                }
            },
            autoLoad: true,
            params: {
                url: url,
                text: text
            },
        },
        buttons: [{
            text: 'Save',
            handler: function() {
                process('submit');
            }
        }, {
            text: 'Delete',
            handler: function() {
                process('delete');                
            }            
        }, {
            text: 'Cancel',
            handler: function() {
                window.close();
            }
        }]
    });
    form.render(document.body);    
});
</script>


</head>
<body>
</body>
</html>
