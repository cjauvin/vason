import traceback, json, datetime
from flask import *
from pymongo import *

def general_error_handler(e):
    tb_str = traceback.format_exc()
    tb_str = tb_str.replace('\n', '<br>')
    return json.dumps({'success' : False, 'error' : 'server', 'error_msg' : tb_str})

application = Flask('vason')
app = application
app.debug = True
app.handle_exception = general_error_handler

@app.route('/submit', methods=['POST'])
def submit():
    request.parameter_storage_class = dict
    db = Connection().vason
    request.form['time'] = datetime.datetime.now()
    key = {'url': request.form['url'], 'text': request.form['text']}
    db.annotations.update(key, request.form, upsert=True, safe=True)    
    json_out = {'success': True}
    return json.dumps(json_out)

@app.route('/retrieve', methods=['POST'])
def retrieve():
    request.parameter_storage_class = dict
    db = Connection().vason
    key = {'url': request.form['url'], 'text': request.form['text']}
    cur = db.annotations.find(key)
    doc = cur[0] if cur.count() > 0 else {}
    if doc:
        del doc['_id']
        del doc['time']
    json_out = {'success': True}
    json_out['data'] = doc
    return json.dumps(json_out)

@app.route('/delete', methods=['POST'])
def delete():
    request.parameter_storage_class = dict
    db = Connection().vason
    request.form['time'] = datetime.datetime.now()
    key = {'url': request.form['url'], 'text': request.form['text']}
    db.annotations.remove(key)
    json_out = {'success': True}
    return json.dumps(json_out)

@app.route('/list', methods=['GET'])
def _list():
    request.parameter_storage_class = dict
    db = Connection().vason
    annotations = sorted([(d['time'], d) for d in db.annotations.find()], reverse=True)
    annotations = [d for (t,d) in annotations]
    for i, doc in enumerate(annotations):
        del annotations[i]['_id']
        annotations[i]['time'] = annotations[i]['time'].isoformat()
    json_out = {'success': True}
    json_out['rows'] = annotations
    json_out['total'] = len(annotations)
    return json.dumps(json_out)
