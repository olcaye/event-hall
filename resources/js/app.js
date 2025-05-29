import './bootstrap';
import $ from 'jquery';
import Dropzone from 'dropzone';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import Swal from 'sweetalert2';

FilePond.registerPlugin(FilePondPluginImagePreview);

window.$ = $;
window.jQuery = $;
window.Dropzone = Dropzone;
window.FilePond = FilePond;
window.Swal = Swal;
