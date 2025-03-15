import './bootstrap';
import { initChat } from './chat';
import Alpine from 'alpinejs';
import tinymce from 'tinymce';
import 'tinymce/models/dom/model';
import 'tinymce/themes/silver';
import 'tinymce/icons/default/icons';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';
import 'tinymce/plugins/wordcount';

window.Alpine = Alpine;
window.tinymce = tinymce;

Alpine.start();

// Clean up TinyMCE instances
function cleanupTinyMCE() {
    tinymce.remove('.tinymce-editor');
}

// Initialize TinyMCE
function initTinyMCE() {
    cleanupTinyMCE(); // Clean up any existing instances first
    tinymce.init({
        selector: '.tinymce-editor',
        height: 300,
        menubar: false,
        setup: function(editor) {
            // Store original content for fullscreen exit recovery
            let originalContent = '';
            
            // Handle fullscreen mode
            editor.on('FullscreenStateChanged', function(e) {
                // Find the closest modal container
                const modalContent = editor.getElement().closest('.inline-block');
                const body = document.querySelector('body');
                
                if (e.state) {
                    // Entering fullscreen mode
                    // Store current content before entering fullscreen
                    originalContent = editor.getContent();
                    
                    if (modalContent) {
                        // For editors inside modals - slightly smaller than 100vw/vh
                        modalContent.style.width = '95vw';
                        modalContent.style.height = '95vh';
                        modalContent.style.maxWidth = 'none';
                        modalContent.style.transform = 'none';
                        modalContent.style.margin = '2vh auto';
                        modalContent.style.position = 'fixed';
                        modalContent.style.top = '0';
                        modalContent.style.left = '0';
                        modalContent.style.right = '0';
                        modalContent.style.zIndex = '9999';
                        
                        // Ensure the editor itself takes full height
                        const editorContainer = editor.getContainer();
                        editorContainer.style.height = 'calc(95vh - 80px)'; // Leave room for header and footer
                        editorContainer.style.display = 'flex';
                        editorContainer.style.flexDirection = 'column';
                        
                        // Make sure the editable area is visible and takes available space
                        const editorArea = editor.getContentAreaContainer();
                        if (editorArea) {
                            editorArea.style.flex = '1';
                            editorArea.style.display = 'flex';
                            editorArea.style.flexDirection = 'column';
                        }
                        
                        // Set a class on body for additional styling
                        body.classList.add('tox-fullscreen-modal-active');
                    }
                } else {
                    // Exiting fullscreen mode
                    if (modalContent) {
                        // For editors inside modals - reset all styles
                        modalContent.style.width = '';
                        modalContent.style.height = '';
                        modalContent.style.maxWidth = '';
                        modalContent.style.transform = '';
                        modalContent.style.margin = '';
                        modalContent.style.position = '';
                        modalContent.style.top = '';
                        modalContent.style.left = '';
                        modalContent.style.right = '';
                        modalContent.style.zIndex = '';
                        
                        // Remove body class
                        body.classList.remove('tox-fullscreen-modal-active');
                        
                        // Get the textarea element and its content
                        const textareaElement = editor.getElement();
                        const textareaId = textareaElement.id;
                        const editorContent = editor.getContent();
                        
                        // Save content to textarea before removing editor
                        textareaElement.value = editorContent;
                        
                        // Remove the editor
                        try {
                            editor.remove();
                        } catch (e) {
                            console.warn('Error removing editor:', e);
                        }
                        
                        // Use a simpler approach - reinitialize with default settings
                        setTimeout(() => {
                            // Reinitialize TinyMCE on this specific textarea with default settings
                            tinymce.init({
                                selector: `#${textareaId}`,
                                height: 300,
                                menubar: false,
                                setup: function(newEditor) {
                                    // Setup event handlers
                                    newEditor.on('Change', function() {
                                        newEditor.save();
                                    });
                                    
                                    const form = newEditor.getElement().closest('form');
                                    if (form) {
                                        form.addEventListener('submit', function() {
                                            newEditor.save();
                                        });
                                    }
                                    
                                    // Handle fullscreen mode for the new editor
                                    newEditor.on('FullscreenStateChanged', function(e) {
                                        const modalContent = newEditor.getElement().closest('.inline-block');
                                        const body = document.querySelector('body');
                                        
                                        if (e.state) {
                                            // Entering fullscreen mode
                                            if (modalContent) {
                                                modalContent.style.width = '95vw';
                                                modalContent.style.height = '95vh';
                                                modalContent.style.maxWidth = 'none';
                                                modalContent.style.transform = 'none';
                                                modalContent.style.margin = '2vh auto';
                                                modalContent.style.position = 'fixed';
                                                modalContent.style.top = '0';
                                                modalContent.style.left = '0';
                                                modalContent.style.right = '0';
                                                modalContent.style.zIndex = '9999';
                                                
                                                const editorContainer = newEditor.getContainer();
                                                editorContainer.style.height = 'calc(95vh - 80px)';
                                                editorContainer.style.display = 'flex';
                                                editorContainer.style.flexDirection = 'column';
                                                
                                                const editorArea = newEditor.getContentAreaContainer();
                                                if (editorArea) {
                                                    editorArea.style.flex = '1';
                                                    editorArea.style.display = 'flex';
                                                    editorArea.style.flexDirection = 'column';
                                                }
                                                
                                                body.classList.add('tox-fullscreen-modal-active');
                                            }
                                        }
                                    });
                                },
                                plugins: 'lists link table code wordcount fullscreen',
                                toolbar: [
                                    { name: 'history', items: [ 'undo', 'redo' ] },
                                    { name: 'styles', items: [ 'styles' ] },
                                    { name: 'formatting', items: [ 'bold', 'italic', 'underline', 'strikethrough' ] },
                                    { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ] },
                                    { name: 'lists', items: [ 'bullist', 'numlist' ] },
                                    { name: 'indentation', items: [ 'outdent', 'indent' ] },
                                    { name: 'insert', items: [ 'link', 'table' ] },
                                    { name: 'tools', items: [ 'code', 'fullscreen' ] }
                                ],
                                content_style: `
                                    body { 
                                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                                        font-size: 14px;
                                        color: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? '#d1d5db' : '#111827'};
                                        background: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? '#1f2937' : '#ffffff'};
                                    }
                                    body.mce-content-body {
                                        scrollbar-width: thin;
                                    }
                                    body.mce-content-body::-webkit-scrollbar {
                                        width: 6px;
                                        height: 6px;
                                    }
                                    body.mce-content-body::-webkit-scrollbar-track {
                                        background: transparent;
                                    }
                                    body.mce-content-body::-webkit-scrollbar-thumb {
                                        background-color: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgb(75 85 99 / 0.5)' : 'rgb(156 163 175 / 0.5)'};
                                        border-radius: 9999px;
                                    }
                                    body.mce-content-body::-webkit-scrollbar-thumb:hover {
                                        background-color: rgb(107 114 128 / 0.7);
                                    }
                                `,
                                skin_url: `/tinymce/skins/ui/${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'}`,
                                content_css: `/tinymce/skins/content/${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'}/content.min.css`,
                                promotion: false,
                                branding: false,
                                relative_urls: false,
                                convert_urls: false,
                                statusbar: false,
                                paste_data_images: true,
                                file_picker_types: '',
                                automatic_uploads: true,
                                theme_url: '/tinymce/themes/silver/theme.js'
                            });
                        }, 300); // Longer delay to ensure DOM is ready
                    }
                }
            });
            
            // Ensure content is saved back to textarea before form submission
            editor.on('Change', function() {
                editor.save();
            });

            // Also save on form submit
            const form = editor.getElement().closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    editor.save();
                });
            }
        },
        plugins: 'lists link table code wordcount fullscreen',
        toolbar: [
            { name: 'history', items: [ 'undo', 'redo' ] },
            { name: 'styles', items: [ 'styles' ] },
            { name: 'formatting', items: [ 'bold', 'italic', 'underline', 'strikethrough' ] },
            { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ] },
            { name: 'lists', items: [ 'bullist', 'numlist' ] },
            { name: 'indentation', items: [ 'outdent', 'indent' ] },
            { name: 'insert', items: [ 'link', 'table' ] },
            { name: 'tools', items: [ 'code', 'fullscreen' ] }
        ],
        content_style: `
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                font-size: 14px;
                color: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? '#d1d5db' : '#111827'};
                background: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? '#1f2937' : '#ffffff'};
            }
            body.mce-content-body {
                scrollbar-width: thin;
            }
            body.mce-content-body::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            body.mce-content-body::-webkit-scrollbar-track {
                background: transparent;
            }
            body.mce-content-body::-webkit-scrollbar-thumb {
                background-color: ${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgb(75 85 99 / 0.5)' : 'rgb(156 163 175 / 0.5)'};
                border-radius: 9999px;
            }
            body.mce-content-body::-webkit-scrollbar-thumb:hover {
                background-color: rgb(107 114 128 / 0.7);
            }
        `,
        skin_url: `/tinymce/skins/ui/${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'}`,
        content_css: `/tinymce/skins/content/${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'}/content.min.css`,
        promotion: false,
        branding: false,
        relative_urls: false,
        convert_urls: false,
        statusbar: false,
        paste_data_images: true,
        file_picker_types: '',
        automatic_uploads: true,
        theme_url: '/tinymce/themes/silver/theme.js',
    });
}

// Initialize chat if we're on a notebook page
document.addEventListener('DOMContentLoaded', () => {
    const notebookShowPage = document.querySelector('[data-notebook-id]');
    if (notebookShowPage) {
        const notebookId = notebookShowPage.dataset.notebookId;
        initChat(notebookId);
    }
    
    // Initialize TinyMCE
    initTinyMCE();

    // Handle modal events
    window.addEventListener('open-modal', (event) => {
        if (event.detail.startsWith('add-note') || event.detail.startsWith('edit-note')) {
            setTimeout(initTinyMCE, 100); // Small delay to ensure modal is rendered
        }
    });

    window.addEventListener('modal-closed', () => {
        cleanupTinyMCE();
    });
});
