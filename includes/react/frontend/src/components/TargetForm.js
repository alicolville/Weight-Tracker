import React from 'react'
import { render } from 'react-dom';
import WeightKg from "./WeightKg";

class TargetForm extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: this.props.target };

    this.handleChange = this.handleChange.bind(this);
  }

  handleChange( new_target ) {
console.log(this.state.target , new_target);
    let update = Object.assign( this.state.target , new_target );

    this.setState({ target: update } );

    //console.log( this.state.target , new_target,update );

    // let target = this.state.target;
    //
    // target[ 'kg' ] = new_target;
    //
    // this.setState( { target: new_target } );
    //
    // console.log( this.state.target );
  }

  handleSave() {


  //  this.props.onClick()
  }

  render() {

    let form_fields;

    if ( 'kg' === this.state.target.format ) {
      form_fields = <WeightKg value={this.props.target['kg']} handleChange={this.handleChange} />
    } else {
      form_fields = <div>Hello</div>
    }

    return(
            <div>
              <h1>{ws_ls_react.locale.target}: {this.state.target.kg}</h1>
              {form_fields}
              <button onClick={ () => this.handleSave() }>{ws_ls_react.locale.save}</button>
            </div>
    );
  }
}

export default TargetForm;
