import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import { Link, useLocation } from 'react-router-dom';
import Pagination from "./Pagination";

const ENDPOINT = "/api/photos";

function PhotoHome(props)
{
   const [currentPage, setCurrentPage] = useState(1);
   const [displayPhotos, setDisplayPhotos] = useState([]);
   const [photos, setPhotos] = useState([]);
   const [keywords, setKeywords] = useState([]);
   const [selectedKeywordId, setSelectedKeywordId] = useState(0);
   const [perPage, setPerPage] = useState(25);
   const [isDeleteMode, setIsDeleteMode] = useState(false);

   const url = useLocation();

   useEffect(() => {
      fetchKeywords();
      fetch();
   }, []);

   useEffect(() => {
      setCurrentPage(1);
      updateDisplayPhotos(0, perPage);
   }, [photos]);

   function deleteImage(photoId)
   {
      axios.delete("/api/photos/"+photoId)
         .then(response => {
            setPhotos(photos.filter(photo => photo.id != photoId));
         })
         .catch(error => {
            console.log(error);
         });
   }

   function fetch() {
      let keywordId = 0;
      let text = "";
      let publicphotos=false;
      let privatephotos=false;
      let fromdate="";
      let todate="";

      if(url.pathname === "/photos/show-search-results") {
         const params = new URLSearchParams(url.search);
         keywordId = parseInt(params.get("keyword"));
         text = params.get("text");
         fromdate = params.get("fromdate");
         todate = params.get("todate");
         publicphotos = params.get("publicphotos");
         privatephotos = params.get("privatephotos");
      } else {
         if(props.match.params.id) {
            keywordId = props.match.params.id;
         }
      }

      if(keywordId     ||
         text          ||
         fromdate      ||
         todate        ||
         publicphotos  ||
         privatephotos)
      {
         axios.get('/api/photos/search', {
            params: {
               keyword_id: keywordId,
               text: text,
               public_checkbox: publicphotos,
               private_checkbox: privatephotos,
               from_date: fromdate,
               to_date: todate
            }
         })
            .then(({data}) => {
               console.log(JSON.stringify(data));
               setPhotos(data.photos);
            })
            .catch(function (error) {
               console.log("fetchKeywords - Error: " + error);
            });
      } else {
         axios.get(ENDPOINT)
            .then(({data}) => {
               setPhotos(data);
            })
            .catch(function (error) {
               console.log("fetch - Error: " + error);
            });
      }
   }

   function fetchKeywords() {
      console.log("In fetch keywords.")
      axios.get('/api/keywords')
         .then(({data}) => {
            setKeywords(data.keywords);
         });
   }

   function keywordSelected(event) {
      console.log("In keywordSelected");
      setSelectedKeywordId(event.target.value);
      axios.get('/api/photos/keyword/'+event.target.value)
         .then(({data}) => {
            setPhotos(data);
         });
   }

   function onPageChange(page)
   {
      setCurrentPage(page);
      let start = (page-1)*this.perPage;
      if (start < 0) {
         start = 0;
      }

      let end = start+this.perPage;
      if(end > photos.length) {
         end = photos.length;
      }
      
      updateDisplayPhotos(start, end);
   }

   function updateDisplayPhotos(start, end)
   {
      setDisplayPhotos(photos.slice(start,end));
   }

   return (
      <div>
         <div className="row">
            <div className="col-12">
               <h1 className="text-center mb-4">Photo Gallery</h1>
               <div className="keyword-search">
                  Keyword Search:
                  <select name="keyword-select" className="ml-1" value={selectedKeywordId} onChange={keywordSelected}>
                     <option disabled value="0">Keywords</option>
                     <option value="0">all</option>
                     {
                        keywords.map((keyword) =>
                           <option key={keyword.id} value={keyword.id}>{keyword.name}</option>
                        )
                     }
                  </select>
                  <span className="ml-3">
                     <button className="btn btn-sm btn-success mr-2" onClick={() => {props.history.push('/photos/add')}}>Add</button>

                     <button className="btn btn-sm btn-success" onClick={() => {setIsDeleteMode(!isDeleteMode)}}>
                        <span>{isDeleteMode ? 'View Mode' : 'Delete Mode'}</span>
                     </button>
                  </span>
               </div>
            </div>
         </div>

         <div className="row">
            {
               displayPhotos.map((photo) =>
                  <div key={photo.id} className="col-8 col-md-6 col-lg-4 col-xl-3">
                     <div className="card mb-4">
                        <div className="card-header">
                           <div className="card-title">
                              <label id="started">Name</label> <Link className="link" to={"/photos/" + photo.id} >{ photo.name }</Link>
                           </div>
                        </div>
                        <div>
                           <div className="card-img">
                              <div className="img-preview mx-auto">
                                 <Link className="link" to={"/photos/" + photo.id} >
                                    <img src={photo.thumbnail_filepath} alt={photo.name} width="200px" height="150px" />
                                 </Link>
                              </div>
                           </div>
                        </div>
                        <div className="card-footer">
                           {isDeleteMode ?
                              <p className="mt-3">
                                 <button className="btn btn-danger" onClick={() => {deleteImage(photo.id)}}>Delete</button>
                              </p>
                              :
                              <p style={{height: 50+'px'}}>
                                 <b>Description</b><br />
                                 { photo.description }
                              </p>
                           }
                        </div>
                     </div>
                  </div>
               )
            }
         </div>
         <div className="row justify-content-center">
            <div className="col-12 col-sm-8 col-md-6 col-lg-4 col-xl-2">
               <Pagination totalItems={photos.length}
                           perPage={perPage}
                           currentPage={currentPage}
                           maxVisibleButtons="3"
                           onPageChange={onPageChange}

               />
            </div>
         </div>
      </div>
    );
}

export default PhotoHome;
