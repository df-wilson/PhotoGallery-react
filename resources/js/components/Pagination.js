import React, {useState, useEffect} from 'react';
import ReactDOM from 'react-dom';

export default function Pagination(props)
{
   const [isInFirstPage, setIsInFirstPage] = useState(true);
   const [isInLastPage, setIsInLastPage] = useState(true);
   const [totalPages, setTotalPages] = useState(Math.ceil(props.totalItems/props.perPage));
   const [visiblePages, setVisiblePages] = useState([]);

   useEffect(() => {
      setTotalPages(Math.ceil(props.totalItems/props.perPage));
   }, [props.totalItems, props.perPage]);

   useEffect(() => {
      setVisiblePages(calculatePages());
      setIsInFirstPage(props.currentPage == 1);
      setIsInLastPage(props.currentPage == totalPages);
   }, [totalPages, props.currentPage]);

   function calculatePages()
   {
      let range = [];
      let visibleButtons = parseInt(props.maxVisibleButtons);
      const startPage = getStartPage();

      if(totalPages < props.maxVisibleButtons) {
         visibleButtons = totalPages;
      }
      
      for(let i = startPage;
          i < startPage + visibleButtons;
          ++i)
      {
         range.push({
            name: i,
            isDisabled: i === parseInt(props.currentPage)
         });
      }
      
      return range;
   }

   function getStartPage()
   {
      let startPage = 1;

      if(props.currentPage === 1) {
         startPage = 1;
      } else if (props.currentPage === totalPages) {
         startPage = totalPages - props.maxVisibleButtons + 1;
      } else {
         startPage = props.currentPage - 1;
      }

      if(startPage < 1) {
         startPage = 1;
      }
      
      return parseInt(startPage);
   }
   
   function onClickFirstPage()
   {
      props.onPageChange(1);
   }

   function onClickPreviousPage()
   {
      props.onPageChange(props.currentPage-1);
   }

   function onClickPage(page)
   {
      props.onPageChange(page);
   }

   function onClickNextPage()
   {
      props.onPageChange(props.currentPage+1);
   }

   function onClickLastPage()
   {
      props.onPageChange(totalPages);
   }

   return (
      <div>
         <h1>Pagination</h1>
         <ul className="pagination">
            <li className="pagination-item">
               <button type="button"
                       className="btn"
                       onClick={onClickFirstPage}
                       disabled={isInFirstPage}
               >
                  First
               </button>
            </li>
            <li className="pagination-item">
               <button type="button"
                       className="btn"
                       onClick={onClickPreviousPage}
                       disabled={isInFirstPage}
               >
                  &lt;
               </button>
            </li>

            {
               visiblePages.map((page) =>
               <li className="pagination-item" key={page.name}>
                  <button type="button"
                          className="btn"
                          onClick={() => onClickPage(page.name)}
                          disabled={page.isDisabled}>
                     {page.name}
                  </button>
               </li>
               )
            }

            <li className="pagination-item">
               <button type="button"
                       className="btn"
                       onClick={onClickNextPage}
                       disabled={isInLastPage}
               >
                  &gt;
               </button>
            </li>
            <li className="pagination-item">
               <button type="button"
                       className="btn"
                       onClick={onClickLastPage}
                       disabled={isInLastPage}
               >
                  Last
               </button>
            </li>
         </ul>
      </div>
   )
}