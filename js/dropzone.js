document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone-container');
    const fileInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    function handleFileSelect(files) {
        imagePreview.innerHTML = '';
          if (files && files.length > 0) {
             for (const file of files) {
                 if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                   reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                         img.classList.add('preview-image');
                         imagePreview.appendChild(img);
                      };
                     reader.readAsDataURL(file);
                } else {
                         const message = document.createElement('p');
                        message.textContent = 'Tipo de archivo no valido para: ' + file.name;
                       imagePreview.appendChild(message);
               }
            }
         }
   }
   dropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
       dropzone.classList.add('dragover');
    });

   dropzone.addEventListener('dragleave', function(e) {
     e.preventDefault();
      dropzone.classList.remove('dragover');
    });

  dropzone.addEventListener('drop', function(e) {
      e.preventDefault();
        dropzone.classList.remove('dragover');
       const files = e.dataTransfer.files;
        handleFileSelect(files);
     });
 dropzone.addEventListener('click', function() {
      fileInput.click();
  });
   fileInput.addEventListener('change', function() {
       handleFileSelect(fileInput.files);
  });
});