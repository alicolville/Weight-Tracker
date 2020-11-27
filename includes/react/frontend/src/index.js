import React from 'react';
import { render } from 'react-dom';
import WeightTracker from './components/WeightTracker';
console.log('here');
render(

  <div>
    <WeightTracker />
  </div>
  ,
  document.querySelector( '#yk-wt-react' ),
);
