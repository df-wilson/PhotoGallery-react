import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter, Route, Switch} from "react-router-dom";

import PhotoAdd from "./components/PhotoAdd.js";
import PhotoHome from "./components/PhotoHome.js";
import PhotoSearch from "./components/PhotoSearch.js";
import PhotoSingle from "./components/PhotoSingle.js";
import NoMatch from "./components/NoMatch.js";

const endpoint = "/api/photos";

function PhotoManager() {
   return (
         <BrowserRouter>
            <Switch>
               <Route component={PhotoHome} path="/" exact />
               <Route component={PhotoHome} path="/home" exact />
               <Route component={PhotoHome} path="/photos/keywords/:id" exact />
               <Route component={PhotoHome} path="/photos/show-search-results" exact />
               <Route component={PhotoAdd} path="/photos/add" exact />
               <Route component={PhotoSearch} path="/photos/search" exact />
               <Route component={PhotoSingle} path="/photos/:id" exact />
               <Route component={NoMatch} path="*" />
            </Switch>
         </BrowserRouter>
   )
}

export default PhotoManager;

if (document.getElementById('photo-manager')) {
   ReactDOM.render(<PhotoManager />, document.getElementById('photo-manager'));
}