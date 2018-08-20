domready(() => {
    const editorEl = document.getElementById('editor');

    const editor = new JSONEditor(editorEl, {
        ajax: true,
        schema: {$ref: '/api/jss'},
        theme: 'bootstrap4',
        iconlib: 'bootstrap4'
    });

    const submitEl = document.getElementById('submit');

    submitEl.onclick = () => {
        const value = editor.getValue();
        console.log(value);
        reqwest({
            url: value['url'],
            method: 'post',
            type: 'json',
            data: JSON.stringify(value['data']),
            complete(resp) {
                console.log(resp)
            }
        });
    };
});
