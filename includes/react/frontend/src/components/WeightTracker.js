import React from 'react'
import { render } from 'react-dom';
import TargetForm from "./TargetForm";

class WeightTracker extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: ws_ls_react.target, data: ws_ls_react.data };
  }

  handleClick() {

   // this.setState( { target: this.state.target['graph-value'] + 10 } )

  }

  render() {
    return(
      <TargetForm target={this.state.target} format={this.state.target.format} onClick={ () => this.handleClick() } />
    );
  }

}

export default WeightTracker;
