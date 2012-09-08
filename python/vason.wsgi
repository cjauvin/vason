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
    db.docs.update(key, request.form, upsert=True, safe=True)    
    json_out = {'success': True}
    return json.dumps(json_out)

@app.route('/retrieve', methods=['POST'])
def retrieve():
    request.parameter_storage_class = dict
    db = Connection().vason
    key = {'url': request.form['url'], 'text': request.form['text']}
    cur = db.docs.find(key)
    doc = cur[0] if cur.count() > 0 else {}
    if doc:
        del doc['_id']
        del doc['time']
    json_out = {'success': True}
    json_out['data'] = doc
    return json.dumps(json_out)

@app.route('/list', methods=['GET'])
def _list():
    request.parameter_storage_class = dict
    db = Connection().vason
    docs = sorted([(d['time'], d) for d in db.docs.find()], reverse=True)
    docs = [d for (t,d) in docs]
    for i, doc in enumerate(docs):
        del docs[i]['_id']
        docs[i]['time'] = docs[i]['time'].isoformat()
    json_out = {'success': True}
    json_out['rows'] = docs
    json_out['total'] = len(docs)
    return json.dumps(json_out)
