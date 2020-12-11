import React from 'react'
import { render } from 'react-dom';
import WeightKg from "./WeightKg";

class TargetForm extends React.Component {

  constructor(props) {
    super(props);
    this.state = { target: this.props.target };

    this.handleChange = this.handleChange.bind(this);
    this.handleSave = this.handleSave.bind(this);
  }

  /**
   * Handle data coming up from the weight fields
   * @param new_target
   */
  handleChange( new_target ) {

    let update = Object.assign( this.state.target , new_target );
    console.log( 'changing in targetform', JSON. stringify(update));

    this.setState({ target: update });
  }

  handleSave() {

    // TODO: Do we actually have a chance in Kg value? If not, may as well stop?

    console.log( 'saving in targetform', JSON. stringify(this.state.target));
    this.props.Save( this.state.target )
  }

  render() {

    let form_fields;

    if ( 'kg' === this.props.target.format ) {
      form_fields = <WeightKg value={this.props.target['kg']} handleChange={this.handleChange} />
    } else {
      form_fields = <div>Hello</div>
    }

    return(
            <div>
              <h1>{ws_ls_react.locale.target}: {this.state.target.display}</h1>
              {form_fields}
              <button onClick={ () => this.handleSave() }>{ws_ls_react.locale.save}</button>
            </div>
    );
  }
}

export default TargetForm;
