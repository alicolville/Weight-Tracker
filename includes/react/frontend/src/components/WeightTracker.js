import React from 'react'
import { render } from 'react-dom';
import TargetForm from "./TargetForm";

class WeightTracker extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: 0, data: ws_ls_react.data };
  }

  handleClick() {

    this.setState( { target: this.state.target + 10 } )

  }

  render() {
    return(
      <TargetForm target={this.state.target} onClick={ () => this.handleClick() } />
    );
  }

}

export default WeightTracker;
