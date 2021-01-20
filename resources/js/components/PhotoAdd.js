import React, {useState, useEffect, createRef} from 'react';
import ReactDOM from 'react-dom';
import { Link } from 'react-router-dom';

const ENDPOINT = "/api/photos";
const STATUS_INITIAL = 0;
const STATUS_SAVING = 1;
const STATUS_SUCCESS = 2;
const STATUS_FAILED = 3;

function PhotoAdd(props)
{
   const fileInput = createRef();
   const [currentStatus, setCurrentStatus] = useState(STATUS_INITIAL);
   const [uploadedFiles, setUploadedFiles] = useState([]);
   const [uploadError, setUploadError] = useState(null);

   function onFileInputChange(event)
   {
      setCurrentStatus(STATUS_SAVING);

      console.log("Files changed. " + fileInput.current.files.length);
      if (fileInput.current.files.length === 0) return;

      let formData = new FormData();

      for (let file = 0; file < fileInput.current.files.length; file++)
      {
         console.log("In onFileInputChange. Filename - " + fileInput.current.files[file].name);
         formData.append("photos[]", fileInput.current.files[file], fileInput.current.files[file].name);
      }

      upload(formData)
         .then(files => {
            setUploadedFiles(files.map(file =>
               <img src={file.url} className="mr-1"></img>
            ));
            setCurrentStatus(STATUS_SUCCESS);
         })
         .catch(err => {
            setUploadError(err.response);
            setCurrentStatus(STATUS_FAILED);
            console.log("Error: " + uploadError);
            console.log("Status: " + "Failed");
         });
   }

   function upload(formData)
   {
      const url = `/api/photos/upload`;
      return axios.post(url, formData)
         .catch(function(error) {
            console.log("PhotoUpload::upload error. " + error);
            setUploadError(error);
         })
         .then(function(response) {
            return response.data.data.map(img => Object.assign({},
               img, { url: `/storage/images/${img.fileName}` }));
         });
   }

   function reset(event)
   {
      setCurrentStatus(STATUS_INITIAL);
      setUploadedFiles([]);
      setUploadError(null);
      event.preventDefault();
   }

   return (
      <div>
         <div className="row">
            <div className="col-12">
               <p>Add photo</p>

               <div id="upload">
                  {
                     (currentStatus == STATUS_INITIAL || currentStatus == STATUS_SAVING) &&
                     <form encType="multipart/form-data" noValidate>
                        <div className="dropbox">
                           <input type="file" multiple ref={fileInput}
                                  name="photos"
                                  onChange={onFileInputChange}
                                  accept="image/*"
                                  className="input-file" />
                           {
                              currentStatus == STATUS_INITIAL &&
                                 <p>
                                    Drag your file(s) here to begin or click to browse
                                 </p>
                           }
                           {
                              currentStatus == STATUS_SAVING &&
                                 <p>
                                    Uploading files...
                                 </p>
                           }
                        </div>
                     </form>
                  }

                  {
                     currentStatus == STATUS_SUCCESS &&
                     <div>
                        <h2>Uploaded {uploadedFiles.length} file(s) successfully.</h2>
                        <p>
                           <a href="#" onClick={reset}>Upload Again</a>
                           <a href="/" onClick={reset} className="ml-2">All Photos</a>
                        </p>
                        {uploadedFiles}
                     </div>
                  }

                  {
                     currentStatus == STATUS_FAILED &&
                        <h2>Status Failed</h2>
                  }
               </div>
            </div>
         </div>
      </div>
   )
}

export default PhotoAdd;
