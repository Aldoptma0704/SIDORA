window.initCkeditor = function (editorSelector, hiddenInputSelector) {
    ClassicEditor.create(document.querySelector(editorSelector), {
        toolbar: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "insertTable",
            "|",
            "undo",
            "redo",
        ],
        table: {
            contentToolbar: ["tableColumn", "tableRow", "mergeTableCells"],
        },
    })
        .then((editor) => {
            console.log("✅ CKEditor initialized");

            const hiddenInput = document.querySelector(hiddenInputSelector);
            editor.model.document.on("change:data", () => {
                hiddenInput.value = editor.getData();
            });

            // Jika hidden input sudah punya nilai lama (misal saat edit), set ke editor
            if (hiddenInput.value) {
                editor.setData(hiddenInput.value);
            }
        })
        .catch((error) => {
            console.error(error);
        });
};
