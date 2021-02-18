import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import { Link } from 'react-router-dom';

function PhotoSearch(props)
{
   const [text, setText] = useState("");
   const [searchPrivate, setSearchPrivate] = useState(true);
   const [searchPublic, setSearchPublic] = useState(false);
   const [keywords, setKeywords] = useState([]);
   const [keywordId, setKeywordId] = useState("");
   const [fromDate, setFromDate] = useState("");
   const [toDate, setToDate] = useState("");

   useEffect(() => {
      fetchKeywords();
   }, []);

   function fetchKeywords()
   {
      axios.get('/api/keywords')
         .then(({data}) => {
            setKeywords(data.keywords);
         });
   }

   function handleSubmit()
   {
      event.preventDefault();
      props.history.push(`/photos/show-search-results?publicphotos=${searchPublic}&privatephotos=${searchPrivate}&keyword=${keywordId}&text=${text}&fromdate=${fromDate}&todate=${toDate}`);
   }

   function reset()
   {
      setText("");
      setSearchPrivate(true);
      setSearchPublic(false);
      setKeywordId("");
      setFromDate("");

      let local = new Date();
      setToDate(local.toJSON().slice(0,10));
   }

   return (
      <section>
         <div className="row">
            <div className="col-12">
               <h1 className="text-center">Search Photos</h1>
               <form id="search-form" onSubmit={handleSubmit}>
                  <input type="hidden" name="_token" value="csrf" />
                  <div className="form-group">
                     <label htmlFor="text-search">Text Search</label>
                     <input type="text"
                            id="text-search"
                            name="text_search"
                            className="form-control"
                            inputMode="text"
                            size="50"
                            value={text}
                            onChange={(event) => {setText(event.target.value)}}
                     />
                  </div>
                  <div className="form-group">
                     <label htmlFor="keyword-id">
                        Keyword Search
                           <select id="keyword-id"
                                   className="form-control"
                                   name="keyword_id"
                                   value={keywordId}
                                   onChange={(event) => {setKeywordId(event.target.value)}}
                                   >
                             <option disabled value="">Keywords</option>
                             <option value="0">all</option>
                              {
                                 keywords.map((keyword) =>
                                    <option key={keyword.id} value={keyword.id}>{keyword.name}</option>
                                 )
                              }
                           </select>
                     </label>
                  </div>

                  <div className="form-group">
                     <label htmlFor="from-date">
                        Photos From
                        <input id="from-date"
                               className="form-control"
                               name="from_date"
                               type="date"
                               value={fromDate}
                               onChange={(event) => {setFromDate(event.target.value)}}
                        />
                     </label>
                     <label htmlFor="to-date">
                        Photos To
                        <input id="to-date"
                               className="form-control"
                               type="date"
                               name="to_date"
                               value={toDate}
                               onChange={(event) => {setToDate(event.target.value)}}
                        />
                     </label>
                  </div>

                  <div className="form-check form-check-inline">
                     <label className="form-check-label"
                            htmlFor="private-checkbox">
                        <input type="checkbox"
                               id="private-checkbox"
                               className="form-check-input"
                               name="private_checkbox"
                               checked={searchPrivate}
                               onChange={(event) => {setSearchPrivate(event.target.checked)}}
                        />
                        My Photos
                     </label>
                  </div>
                  <div className="form-check form-check-inline">
                     <label className="form-check-label"
                            htmlFor="public-checkbox">
                        <input type="checkbox"
                               id="public-checkbox"
                               className="form-check-input"
                               name="public_checkbox"
                               checked={searchPublic}
                               onChange={(event) => {setSearchPublic(event.target.checked)}}
                        />
                        Public Photos
                     </label>
                  </div>

                  <div id="form-buttons" className="mt-3">
                     <button type="button"
                             className="btn btn-primary mr-2"
                             onClick={reset}>
                        Reset
                     </button>
                     <button type="submit"
                             className="btn btn-primary">
                        Submit!
                     </button>
                  </div>
               </form>
            </div>
         </div>
      </section>
   );
}

export default PhotoSearch;