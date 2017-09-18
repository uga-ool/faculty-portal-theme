var oswald = new FontFaceObserver('Oswald', {
  weight: 600
});
var merriweather = new FontFaceObserver('Merriweather', {
  weight: 400
});
var merriweathersans = new FontFaceObserver('Merriweather Sans', {
  weight: 400
});

Promise.all([oswald.load(), merriweather.load(), merriweathersans.load()]).then(function () {
  document.documentElement.className += " fonts-loaded";
});