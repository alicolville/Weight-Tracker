import React from 'react'
import { render } from 'react-dom';
import TargetForm from "./TargetForm";

class WeightTracker extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: ws_ls_react.target, data: ws_ls_react.data };
  }

  handleClick( new_target ) {

    console.log( 'click in WeightTracker.js: ' + new_target )

    this.setState( { target: new_target } )

  }

  render() {
    return(
      <TargetForm target={this.state.target} format={this.state.target.format} onClick={ () => this.handleClick() } />
    );
  }

}

export default WeightTracker;
