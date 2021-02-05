import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import {useHistory} from "react-router-dom";

const endpoint = "/api/photos/";

function PhotoSingle(props)
{
   const [photo, setPhoto] = useState({});
   const [allKeywords, setAllKeywords] = useState([]);
   const [keywords, setKeywords] = useState([]);
   const [isUpdateDescButtonShown, setIsUpdateDescButtonShown] = useState(false);
   const [isEditTitle, setIsEditTitle] = useState(false);
   const [newKeyword, setNewKeyword] = useState("");
   const [showKeywordAddForm, setShowKeywordAddForm] = useState(false);
   const [showEditKeywords, setShowEditKeywords] = useState(false);

   const history = useHistory();

   useEffect(() => {
      fetchPhoto();
      fetchPhotoKeywords(props.match.params.id);
      fetchAllKeywords();
   }, []);

   function fetchPhoto()
   {
      if(props.match.params.id)
      {
         axios.get(endpoint+props.match.params.id)
              .then(({data}) => {
                 setPhoto(data);
                 console.log(JSON.stringify(data));
              });
      } else {
         alert("No matching photo id");
      }
   }

   function fetchAllKeywords()
   {
      axios.get('/api/keywords')
         .then(({data}) => {
            setAllKeywords(data.keywords);
         })
         .catch(function (error) {
            console.log("fetchAllKeywords - Error: " + error);
         });
   }

   function fetchPhotoKeywords(photoId)
   {
      if(photoId) {
         axios.get(endpoint+photoId+'/keywords')
            .then(({data}) => {
               setKeywords(data.keywords);
            })
            .catch(function (error) {
               console.log("fetchKeywords - Error: " + error);
            });
      } else {
         setKeywords([]);
      }
   }

   function handleTitleChange(event)
   {
      setPhoto({...photo, name: event.target.value});
   }

   function handleDescriptionChange(event)
   {
      setPhoto({...photo, description: event.target.value});
   }

   function showUpdateButton()
   {
      setIsUpdateDescButtonShown(true);
   }

   function submitDescription(event)
   {
      axios.post('/api/photos/'+photo.id+'/description', {
         description: photo.description
      })
         .then(function (response) {
            console.log(response);
         })
         .catch(function (error) {
            console.log(error);
         });
   }

   function editTitle(event)
   {
      setIsEditTitle(true);
   }

   function submitTitle(event)
   {

      axios.post('/api/photos/'+photo.id+'/title', {
         title: photo.name
      })
         .then(function (response) {
         })
         .catch(function (error) {
            console.log(error);
         });

      cancelUpdateTitle();

   }

   function cancelUpdateTitle(event)
   {
      setIsEditTitle(false);
   }

   function showNextPhoto(event)
   {
      if(photo.id)
      {
         axios.get(endpoint+photo.id+"/next")
            .then(({data}) => {
               console.log("showNextPhoto: "+JSON.stringify(data));
               setPhoto(data);
               fetchKeywords(data.id);
            });
      } else {
         alert("No matching photo id");
      }
   }

   function showPreviousPhoto()
   {
      if(photo.id)
      {
         axios.get(endpoint+photo.id+"/prev")
            .then(({data}) => {
               if(typeof(data)==='object' && !Array.isArray(data)) {
                  setPhoto(data);
                  fetchKeywords(data.id);
               }
            })
            .catch(function (error) {
               console.log("showPreviousPhoto - Error: " + error);
            });
      } else {
         alert("No matching photo id");
      }
   }

   function showAddKeyword(event)
   {

   }

   function submitKeyword(event)
   {
      event.preventDefault();

      const trimmedKeyword = newKeyword.trim();
      
      if(trimmedKeyword.length < 1) {
         return;
      }

      axios.post('/api/keywords/photo/' + photo.id, {
         keyword: trimmedKeyword
      })
         .then((response) => {
            if(response.status == 201) {
               setKeywords(keywords.concat({"id":response.data.keyword_id, "name": trimmedKeyword}));
            }
         })
         .catch(function (error) {
            console.log(error);
         });

      setNewKeyword("");
   }

   function removeKeyword(keywordId)
   {
      axios.delete('/api/keywords/'+keywordId+'/photo/'+photo.id)
         .then(({data}) => {
            let newKeywords = keywords.filter((element) => element.id != keywordId);
            setKeywords(newKeywords);
            console.log("Keywords: "+ JSON.stringify(keywords));
         });
   }

   function checkKeywordInputForEnter(event)
   {
      if(event.key === 'Enter'){
         event.preventDefault();

         submitKeyword(event);
      }
   }

   function submitTogglePublic()
   {

   }

   return (
      <section>
         <div className="row">
            <div className="col-12">
               { !isEditTitle &&
                  <div id="img-title" onMouseDown={editTitle}>
                     <h1 id="img-title-text" className="text-center">{photo.name}</h1>
                  </div>
               }
               { isEditTitle &&
                  <div id="img-title-edit">
                     <input id="img-title-input" type="text" required size="30" value={photo.name} onChange={handleTitleChange} />
                     <button id="img-title-update-btn" className="btn btn-primary btn-sm mr-1" type="button" onClick={submitTitle}>Update</button>
                     <button id="img-title-cancel-btn" className="btn btn-primary btn-sm" type="button" onClick={cancelUpdateTitle}>Cancel</button>
                  </div>
               }
            </div>
         </div>
         <div className="row">
            <div className="col-12 col-md-9 col-lg-10">
               <div className="img-area text-center">
                  <img src={photo.filepath} alt={photo.name} className="responsive-image" />
               </div>

               <h2>Description</h2>
               <textarea id="desc-text" className="img-desc form-control" name="textarea" value={photo.description} rows="4" cols="40" onFocus={showUpdateButton} onChange={handleDescriptionChange} />
               {
                  isUpdateDescButtonShown &&
                  <button id="desc-update-button" className="btn btn-primary btn-sm mt-1" type="button" onClick={submitDescription}>Update</button>
               }
            </div>
            <div className="col-12 col-md-3 col-lg-2">
               <div id="keyword-div">
                  <h2>Keywords
                     { showEditKeywords &&
                        <button className="btn btn-xs btn-primary ml-1" type="button" onClick={() => setShowKeywordAddForm(true)}>+</button>
                     }

                     { !showEditKeywords &&
                        <span id="keyword-edit-link">(<a href="#edit" onClick={() => setShowEditKeywords(true)}>edit</a>)</span>
                     }
                  </h2>

                  {
                     showKeywordAddForm &&
                     <div id="add-keyword-form">
                        <input id="keyword-input" type="text" size="20" list="keyword-options" value={newKeyword} onKeyPress={checkKeywordInputForEnter} onChange={() => setNewKeyword(event.target.value)}/>
                        <datalist id="keyword-options">
                           {allKeywords.map((keyword) =>
                              <option key={keyword.id} value={keyword.name} />
                           )}
                        </datalist>
                        <button id="keyword-update-btn" className="btn btn-primary btn-sm mr-1" type="button" onClick={submitKeyword}>Add</button>
                        <button id="keyword-done-btn" className="btn btn-primary btn-sm" type="button" onClick={() => setShowKeywordAddForm(false)}>Done</button>
                     </div>
                  }

                  {keywords.map((keyword) =>
                  <div id={`keyword${keyword.id}`} key={keyword.id} className="mb-1">
                     <button className="btn btn-xs btn-light" onClick={() => history.push("/photos/keywords/"+keyword.id)}>{keyword.name}</button>

                     {
                        showEditKeywords &&
                        <button className="btn btn-xs btn-danger keyword-edit" type="button"
                                onClick={() => removeKeyword(keyword.id)}>x</button>
                     }
                  </div>
                  )}
               </div>

               <div id="public-toggle-div">
                  <input type="checkbox" id="public-checkbox" name="public-checkbox" onChange={submitTogglePublic} />
                  <label htmlFor="public-checkbox">Allow public</label>
               </div>
               <div id="navigation-section">
                  <button className="btn btn-light" onClick={showPreviousPhoto}>⇦</button>
                  <button className="btn btn-light" onClick={showNextPhoto}>⇨</button>
               </div>
            </div>
         </div>

      </section>
   )
}

export default PhotoSingle;
