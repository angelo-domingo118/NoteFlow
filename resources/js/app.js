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
import 'tinymce/plugins/image';
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
            // Handle fullscreen mode
            editor.on('FullscreenStateChanged', function(e) {
                const modalContent = editor.getElement().closest('.inline-block');
                if (modalContent) {
                    if (e.state) {
                        modalContent.style.width = '100vw';
                        modalContent.style.height = '100vh';
                        modalContent.style.maxWidth = 'none';
                        modalContent.style.transform = 'none';
                        modalContent.style.margin = '0';
                    } else {
                        modalContent.style.width = '';
                        modalContent.style.height = '';
                        modalContent.style.maxWidth = '';
                        modalContent.style.transform = '';
                        modalContent.style.margin = '';
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
        plugins: 'lists link image table code wordcount fullscreen',
        toolbar: [
            { name: 'history', items: [ 'undo', 'redo' ] },
            { name: 'styles', items: [ 'styles' ] },
            { name: 'formatting', items: [ 'bold', 'italic', 'underline', 'strikethrough' ] },
            { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ] },
            { name: 'lists', items: [ 'bullist', 'numlist' ] },
            { name: 'indentation', items: [ 'outdent', 'indent' ] },
            { name: 'insert', items: [ 'link', 'image', 'table' ] },
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
        file_picker_types: 'image',
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
