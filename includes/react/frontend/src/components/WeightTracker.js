import React from 'react'
import { render } from 'react-dom';
import TargetForm from "./TargetForm";

class WeightTracker extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: ws_ls_react.target, data: ws_ls_react.data };

    this.TargetSave = this.TargetSave.bind(this);
  }

  TargetSave( new_target ) {


    console.log( new_target );
    this.setState( { target: new_target } )
    console.log( 'click in WeightTracker.js: ' +  this.state.target );
  }

  render() {
    return(
      <TargetForm target={this.state.target} format={this.state.target.format} Save={ () => this.TargetSave() } />
    );
  }

}

export default WeightTracker;
