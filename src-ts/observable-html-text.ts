type ObservableCallback<O> = (value: O) => any;
class Observable<O> {
  #v: O;
  set value(v: O) {
    this.#v = v;
    this.notify();
  }
  get value(): O {return this.#v}
  
  constructor(startValue: O) {
    this.#v = startValue;
  }
  
  listeners: ObservableCallback<O>[] = [];
  addListener(callback: ObservableCallback<O>) {
    this.listeners.push(callback);
  }
  
  notify() {
    for (let i = 0; i < this.listeners.length; i++) {
      this.listeners[i](this.#v);
    }
  }
}

function htmlText(stationary: string[], ...observables: Observable<any>[]): Text[] {
  const r: Text[] = [];
  
  for (let i = 0; i < observables.length; i++) {
    if (stationary[i] !== "") {
      r.push(document.createTextNode(stationary[i]));
    }
    
    const text = document.createTextNode(observables[i].value);
    observables[i].addListener((v) => {
      text.textContent = v;
    });
    
    r.push(text);
  }
  
  if (stationary[stationary.length - 1] !== "") {
    r.push(document.createTextNode(stationary[stationary.length - 1]));
  }
  
  return r;
}