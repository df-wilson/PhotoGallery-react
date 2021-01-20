import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import { Link } from 'react-router-dom';

const ENDPOINT = "/api/photos";

function PhotoHome(props) {
   const [photos, setPhotos] = useState([]);
   const [keywords, setKeywords] = useState([]);
   const [selectedKeywordId, setSelectedKeywordId] = useState(0);
   const [pageCount, setPageCount] = useState(1);

   useEffect(() => {
      fetchKeywords();
      fetch();
   }, []);

   function fetch() {
      let keywordId = 0;
      let text = "";
      let publicphotos=false;
      let privatephotos=false;
      let fromdate="";
      let todate="";
      if(props.match.params.id) {
         keywordId = props.match.params.id;
      }
      /*if(this.keywordid ||
         this.text ||
         this.fromdate ||
         this.todate ||
         this.privatephotos ||
         this.publicphotos) */
      if(keywordId)
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
               console.log("fetchKeywords - Error: " + error);
            });
      }
   }

   function fetchKeywords() {
      axios.get('/api/keywords')
         .then(({data}) => {
            setKeywords(data.keywords);
         });
   }

   function keywordSelected(event) {
      setSelectedKeywordId(event.target.value);
      axios.get('/api/photos/keyword/'+event.target.value)
         .then(({data}) => {
            setPhotos(data);
         });
   }
   
   return (
      <div>
         <div className="row">
            <div className="col-12">
               <h1 className="text-center">Photo Gallery</h1>
               <div className="keyword-search mb-2">
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
                     <button className="btn btn-success" onClick={() => {props.history.push('/photos/add')}}>Add</button>
                  </span>
               </div>
            </div>
         </div>

         <div className="row">
            {
               photos.map((photo) =>
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
                           <p style={{height: 50+'px'}}>
                              <b>Description</b><br />
                              { photo.description }
                           </p>
                        </div>
                     </div>
                  </div>
               )
            }
         </div>
      </div>
    );
}

export default PhotoHome;
