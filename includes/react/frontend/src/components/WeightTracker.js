import React from 'react'
import { render } from 'react-dom';
import TargetForm from "./TargetForm";

class WeightTracker extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: ws_ls_react.target, data: ws_ls_react.data };
    console.log( 'loading in WeightTracker.js: ' +  JSON.stringify( this.state.target ), this.state.target.format );
    this.TargetSave = this.TargetSave.bind(this);
  }

  TargetSave( new_target ) {

    console.log( 'saving (NEW WEIGHT)in WeightTracker.js: ' +  JSON.stringify( new_target ) );
    this.setState( { target: new_target } )
    console.log( 'saving in WeightTracker.js: ' +  JSON.stringify( this.state.target ), this.state.target.format );
  }

  render() {
    return(
      <TargetForm target={this.state.target} format={this.state.target.format} Save={ this.TargetSave() } />
    );
  }

}

export default WeightTracker;
