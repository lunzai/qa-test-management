<script>
    // import QuillImageDropAndPaste from 'quill-image-drop-and-paste/src/QuillImageDropAndPaste';
    // import QuillImageDropAndPaste from 'quill-image-drop-and-paste';
    import * as alert from '../../components/alert/alert.js';
    import moment from 'moment';
    import { onMount } from "svelte";
    import { stores } from '@sapper/app';
    import 'quill/dist/quill.snow.css';
    import * as fileApi from '@app/api/file';
    
    export let model;
    export let attribute;
    export let label;
    export let placeholder;
    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;
    export let token;
    export let maxHeight = 800;

    let quill;
    let editor;
    if (label === undefined) {
        label = model.getLabel(attribute);
    }
    $: error = model.errors[attribute];
    let content = model.get(attribute);

    const options = {
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, false] }],
                [
                    { 'color': [] }, { 'background': [] }, 
                    'bold', 'italic', 'underline', 'strike', 
                ],                
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'code-block', 'clean']
            ],
            imageDropAndPaste: {
                // add an custom image handler
                handler: imageHandler
            },
            // imageUpload: {
            //     accept: ['image/jpeg', 'image/png', 'image/jpg'],
            //     maxSize: 10 * 1024 * 1024, // in bytes
            //     invalid(file) { // called if image is not in array of accept or greater than maxSize
            //         alert.danger(`Upload failed: ${file.name} must be JPG or PNG & lesser than 10MB`);
            //     },
            //     upload(file) { // required
            //         const formData = new FormData();
            //         formData.append('file', file);
            //         uploadImage(formData).then(path => {
            //             if (path) {
            //                 const index = (quill.getSelection() || {}).index || quill.getLength();
            //                 if (index) {
            //                     resolve(path);
            //                 }
            //             }
            //             return;
            //         });
            //     }
            // }
        },
        placeholder,
        theme: "snow"
    }

    async function imageHandler(dataUrl, type, imageData) {
        const file = imageData.toFile();
        var formData = new FormData();
        formData.append('file', file);
        uploadImage(formData).then(path => {
            if (path) {
                const index = (quill.getSelection() || {}).index || quill.getLength();
                if (index) {
                    quill.insertEmbed(index, 'image', path, 'user')
                }
            }
            return;
        });
    }

    async function uploadImage(data) {
        const response = await fileApi.uploadWysiwyg(data, token);
        if (response.success) {
            return response.data.absolute_path;
        } else {
            if (response.status == 422) {
                alert.danger(`Upload failed: ${response.data[0].message}`);
            } else {
                alert.danger(`Upload failed`);
            }
            return false;
        }
    }

    function generateFileName(type) {
        const name = (Math.random() + 1 / moment().unix()).toString(36).substr(2, 8);
        let ext;
        switch (type) {
            case 'image/png':
                ext = 'png';
                break;
            case 'image/jpeg':
            case 'image/jpg':
                ext = 'jpg';
                break;
        }
        return `${name}.${ext}`;
    }

    onMount(async () => {
        const { default: Quill } = await import("quill");
        const { default: QuillImageDropAndPaste } = await import('quill-image-drop-and-paste');
        Quill.register('modules/imageDropAndPaste', QuillImageDropAndPaste);
        // Quill.register('modules/imageUpload', ImageUploadModule);
        quill = new Quill(editor, options);
        const container = editor.getElementsByClassName("ql-editor")[0];
        quill.on("text-change", function(delta, oldDelta, source) {
            console.log('text change')
            model.set(attribute, container.innerHTML);
            model = model;
        });
        quill.getModule('toolbar').addHandler('image', function(clicked) {
            if (clicked) {
                let fileInput = quill.container.querySelector('input.ql-image[type=file]')
                if (fileInput == null) {
                    fileInput = document.createElement('input')
                    fileInput.setAttribute('type', 'file')
                    fileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg')
                    fileInput.classList.add('ql-image')
                    fileInput.addEventListener('change', function(e) {
                        let files = e.target.files, file;
                        if (files.length > 0) {
                            file = files[0];
                            let type = file.type;
                            let reader = new FileReader();
                            reader.onload = (e) => {
                                // handle the inserted image
                                let dataUrl = e.target.result;
                                imageHandler(dataUrl, type, new QuillImageDropAndPaste.ImageData(dataUrl, type));
                                fileInput.value = ''
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }
                fileInput.click();
            }
        });
    });
</script>

<style>
</style>

<div 
    data-cy="editor-{attribute}-wrapper" 
    id="editor-{model.getId()}" 
    class="editor-wrapper {containerClass}"
>
    {#if label}
        <label 
            data-cy="label-{attribute}"
            class="block text-gray-700 tracking-wide font-medium mb-2 {labelClass ? labelClass:''}" for={attribute}>
            {label}
        </label>
    {/if}
    <div 
        data-cy="editor-{attribute}"
        class="editor overflow-y-auto {inputClass ? inputClass : ''}" 
        style={maxHeight ? `max-height: ${maxHeight}` : ''} 
        bind:this={editor} 
    >
        {@html content ? content : ''}
    </div>
    {#if error}
        <p 
            data-cy="editor-{attribute}-error" 
            class="text-red-500 text-sm mt-1 {errorClass ? errorClass:''}"
        >{error}</p>
    {/if}
</div>

