/**
 * SESSION 68 - Upload Premium Manager (FIXED SYNTAX)
 */

console.log(' upload-premium.js chargé !');

class UploadManager {
    constructor() {
        console.log(' UploadManager constructor appelé');
        this.files = [];
        this.maxFiles = 10;
        this.maxSizeImage = 5 * 1024 * 1024;
        this.maxSizeDocument = 10 * 1024 * 1024;
        this.allowedExtensions = {
            image: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            document: ['pdf', 'doc', 'docx', 'txt']
        };
        
        this.init();
    }
    
    init() {
        console.log(' Initialisation UploadManager...');
        
        this.dropZone = document.getElementById('drop-zone');
        this.fileInput = document.getElementById('file-input');
        this.fileList = document.getElementById('file-list');
        this.uploadAllBtn = document.getElementById('upload-all-btn');
        this.browseBtn = document.getElementById('browse-btn');
        
        console.log(' Éléments DOM:', {
            dropZone: !!this.dropZone,
            fileInput: !!this.fileInput,
            browseBtn: !!this.browseBtn
        });
        
        if (!this.fileInput) {
            console.error(' Input file introuvable !');
            return;
        }
        
        this.bindEvents();
        this.updateStats();
        
        console.log(' UploadManager OK !');
    }
    
    bindEvents() {
        console.log(' Attachement events...');
        
        // Drag & Drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, () => {
                this.dropZone.classList.add('drag-over');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, () => {
                this.dropZone.classList.remove('drag-over');
            });
        });
        
        this.dropZone.addEventListener('drop', (e) => {
            console.log(' Drop !');
            this.handleDrop(e);
        });
        
        // Bouton Parcourir (FIX)
        if (this.browseBtn) {
            this.browseBtn.addEventListener('click', (e) => {
                console.log(' Bouton Parcourir cliqué !');
                e.preventDefault();
                e.stopPropagation();
                this.fileInput.click();
            });
        }
        
        // Drop zone click
        this.dropZone.addEventListener('click', (e) => {
            if (e.target.id === 'browse-btn' || e.target.id === 'upload-all-btn') {
                return;
            }
            this.fileInput.click();
        });
        
        // FILE INPUT CHANGE (CRITIQUE)
        this.fileInput.addEventListener('change', (e) => {
            console.log(' CHANGE EVENT !');
            console.log(' Files:', e.target.files.length);
            
            if (e.target.files.length === 0) {
                console.warn(' Aucun fichier !');
                return;
            }
            
            Array.from(e.target.files).forEach((file, i) => {
                console.log((i+1) + '. ' + file.name + ' (' + this.formatFileSize(file.size) + ')');
            });
            
            this.handleFiles(e.target.files);
        });
        
        // Upload all
        this.uploadAllBtn.addEventListener('click', () => {
            this.uploadAll();
        });
        
        console.log('✅ Events OK !');
    }
    
    handleDrop(e) {
        const files = e.dataTransfer.files;
        console.log('📥 Dropped:', files.length);
        this.handleFiles(files);
    }
    
    handleFiles(files) {
        console.log(' Processing:', files.length);
        
        if (this.files.length + files.length > this.maxFiles) {
            alert('Maximum ' + this.maxFiles + ' fichiers');
            return;
        }
        
        let added = 0;
        
        Array.from(files).forEach(file => {
            const validation = this.validateFile(file);
            if (validation.valid) {
                const fileObj = {
                    id: Date.now() + Math.random(),
                    file: file,
                    status: 'pending',
                    progress: 0
                };
                this.files.push(fileObj);
                this.renderFile(fileObj);
                added++;
                console.log(' Ajouté: ' + file.name);
            } else {
                alert(validation.error);
                console.error(' Rejeté: ' + validation.error);
            }
        });
        
        console.log(' ' + added + ' fichiers ajoutés !');
        
        if (added > 0) {
            this.uploadAllBtn.style.display = 'block';
        }
        
        this.updateStats();
        this.fileInput.value = '';
    }
    
    validateFile(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        const isImage = this.allowedExtensions.image.includes(ext);
        const isDoc = this.allowedExtensions.document.includes(ext);
        
        if (!isImage && !isDoc) {
            return { valid: false, error: 'Format non supporté: .' + ext };
        }
        
        const maxSize = isImage ? this.maxSizeImage : this.maxSizeDocument;
        if (file.size > maxSize) {
            const maxMB = maxSize / (1024 * 1024);
            return { valid: false, error: file.name + ' dépasse ' + maxMB + 'MB' };
        }
        
        return { valid: true };
    }
    
    renderFile(fileObj) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.dataset.fileId = fileObj.id;
        
        const isImage = fileObj.file.type.startsWith('image/');
        
        const preview = isImage ? '<img class="preview-img" alt="Preview">' : this.getFileIcon(fileObj.file.name);
        
        fileItem.innerHTML = '<div class="file-preview">' + preview + '</div>' +
            '<div class="file-info">' +
            '<div class="file-name">' + fileObj.file.name + '</div>' +
            '<div class="file-size">' + this.formatFileSize(fileObj.file.size) + '</div>' +
            '<div class="file-progress">' +
            '<div class="progress-bar"><div class="progress-fill" style="width: 0%"></div></div>' +
            '<div class="progress-text">0%</div>' +
            '</div></div>' +
            '<button class="file-remove" data-file-id="' + fileObj.id + '"></button>';
        
        this.fileList.appendChild(fileItem);
        
        if (isImage) {
            this.generatePreview(fileObj.file, fileItem.querySelector('.preview-img'));
        }
        
        fileItem.querySelector('.file-remove').addEventListener('click', () => {
            this.removeFile(fileObj.id);
        });
    }
    
    generatePreview(file, imgElement) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imgElement.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = { pdf: '', doc: '', docx: '', txt: '' };
        return '<span class="file-icon">' + (icons[ext] || '') + '</span>';
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    removeFile(fileId) {
        const index = this.files.findIndex(f => f.id === fileId);
        if (index !== -1) {
            const fileItem = document.querySelector('[data-file-id="' + fileId + '"]');
            if (fileItem) {
                fileItem.remove();
                this.files.splice(index, 1);
                this.updateStats();
                
                if (this.files.length === 0) {
                    this.uploadAllBtn.style.display = 'none';
                }
            }
        }
    }
    
    async uploadAll() {
        const pending = this.files.filter(f => f.status === 'pending');
        
        if (pending.length === 0) {
            alert('Aucun fichier à envoyer');
            return;
        }
        
        this.uploadAllBtn.disabled = true;
        console.log(' Upload ' + pending.length + ' fichiers...');
        
        for (const fileObj of pending) {
            await this.uploadFile(fileObj);
        }
        
        this.uploadAllBtn.disabled = false;
        alert('Envoi terminé !');
    }
    
    async uploadFile(fileObj) {
        const fileItem = document.querySelector('[data-file-id="' + fileObj.id + '"]');
        const progressFill = fileItem.querySelector('.progress-fill');
        const progressText = fileItem.querySelector('.progress-text');
        
        fileObj.status = 'uploading';
        fileItem.classList.add('uploading');
        
        for (let progress = 0; progress <= 100; progress += 10) {
            await new Promise(resolve => setTimeout(resolve, 200));
            fileObj.progress = progress;
            progressFill.style.width = progress + '%';
            progressText.textContent = progress + '%';
        }
        
        fileObj.status = 'success';
        fileItem.classList.remove('uploading');
        fileItem.classList.add('success');
        
        this.updateStats();
    }
    
    updateStats() {
        const total = this.files.length;
        const uploaded = this.files.filter(f => f.status === 'success').length;
        const totalSize = this.files.reduce((sum, f) => sum + f.file.size, 0);
        
        document.getElementById('total-files').textContent = total;
        document.getElementById('uploaded-files').textContent = uploaded;
        document.getElementById('total-size').textContent = this.formatFileSize(totalSize);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log(' DOM ready !');
    window.uploadManager = new UploadManager();
});