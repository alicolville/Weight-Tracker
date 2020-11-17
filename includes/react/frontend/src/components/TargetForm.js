import React from 'react'
import { render } from 'react-dom';
import WeightKg from "./WeightKg";

class TargetForm extends React.Component {

  constructor(props) {
    super(props);
    // this.state = {
    //   target: 0,
    // };
  }

  render() {
    return(
            <div>
              <h1>{ws_ls_react.locale.target}: {this.props.target}</h1>
              <WeightKg value={this.props.target}/>
              <button onClick={ () => this.props.onClick() }>{ws_ls_react.locale.save}</button>
            </div>
    );
  }
}

export default TargetForm;
