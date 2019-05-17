import * as React from 'react';

// stripe needs to be imported via <script> (no client-side NPM package available)
// in the blade template
declare var stripe: any;

interface InitialState {
    selectedOption: DonationAmountType,
    selectedAmount: number,
    customAmount: number,
    stripeKey: string,
    csrfToken: string,
    submitRoute: string,
};

enum DonationAmountType {
    SetAmount,
    CustomAmount,
}

export default class Component extends React.Component<{}, InitialState> {
    state: InitialState = {
        selectedOption: DonationAmountType.SetAmount,
        selectedAmount: 3000,
        customAmount: 0,
        stripeKey: "",
        csrfToken: "",
        submitRoute: "",
    };

    private customAmountInput: HTMLInputElement; 
    private form: HTMLFormElement;

    componentDidMount() {
        // grab Stripe key from meta content, because we
        // can't pass it via props from Blade
        const stripeKey   = document.head.querySelector('[name=stripe-key]').getAttribute('content');
        const submitRoute = document.head.querySelector('[name=stripe-submit]').getAttribute('content');
        const csrfToken   = document.head.querySelector('[name=csrf-token]').getAttribute('content');
        
        this.setState({ 
            stripeKey: stripeKey,
            submitRoute: submitRoute,
            csrfToken: csrfToken,
        });
    }

    useCustomAmount = () => {
        this.setState({
            selectedOption: DonationAmountType.CustomAmount,
        }, () => {
            this.customAmountInput.focus();
        });
    }

    useSetAmount = (amount: number) => {
        this.setState({
            selectedOption: DonationAmountType.SetAmount,
            selectedAmount: amount,
        });
    }

    handleCustomAmountChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = Number(event.target.value);
        if (!Number.isNaN(newValue) && newValue >= 0 && newValue < 999999) {
            this.setState({
                customAmount: newValue, 
            });
        }
    }

    isSetAmount = (amount: Number): Boolean => {
        return this.state.selectedOption == DonationAmountType.SetAmount &&
               this.state.selectedAmount == amount;
    }

    isCustomAmount = (): Boolean => {
        return this.state.selectedOption == DonationAmountType.CustomAmount;
    }

    isCheckoutButtonDisabled = (): boolean => {
        if (this.state.selectedOption == DonationAmountType.SetAmount) {
            return false;
        } 
        return this.state.customAmount <= 0;
    }

    getAmount = (): string => {
        if (this.state.selectedOption == DonationAmountType.SetAmount) {
            return this.state.selectedAmount.toString();
        } else {
            return this.state.customAmount.toString();
        }
    }

    proceedToCheckout = () => {
        alert('test');
    }

    render() {
        return (
            <div>
                <div className="button-table">
                    <div className="button-table__row">
                        <button className={this.isSetAmount(500) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(500)}>$5</button>
                        <button className={this.isSetAmount(1000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(1000)}>$10</button>
                        <button className={this.isSetAmount(2000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(2000)}>$20</button>
                    </div>
                    <div className="button-table__row">
                        <button className={this.isSetAmount(3000) ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={() => this.useSetAmount(3000)}>$30</button>
                        <button className={this.isCustomAmount() ? "button button--fill button--accent" : "button button--fill button--secondary"} onClick={this.useCustomAmount}>
                            <i className="fas fa-keyboard"></i> Custom
                        </button>
                    </div>
                </div>
                
                <div className="input-container">
                    <div className="input-prefix"><i className="fas fa-dollar-sign"></i></div>
                    <div className="input-suffix">USD</div>

                    <input
                        ref={input => { this.customAmountInput = input }}
                        className="input-text input-text--prefixed" 
                        type="text" 
                        placeholder="3.00" 
                        disabled={this.state.selectedOption == DonationAmountType.SetAmount} 
                        value={this.state.customAmount.toString()}
                        onChange={this.handleCustomAmountChange}
                        />
                </div>

                    <input type="hidden" name="_token" value={this.state.csrfToken} />
                    <input type="hidden" name="stripe_amount_in_cents" value={this.getAmount()} />

                    <button className="button button--large button--fill button--primary" type="button" disabled={this.isCheckoutButtonDisabled()} onClick={this.proceedToCheckout}>
                        <i className="fas fa-credit-card"></i> Proceed to Stripe Checkout
                    </button>
            </div>
        );
    }
}