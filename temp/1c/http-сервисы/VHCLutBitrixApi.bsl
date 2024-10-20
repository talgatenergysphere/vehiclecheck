// BSLLS:LatinAndCyrillicSymbolInWord-off
// BSLLS:MagicNumber-off
// BSLLS:IfConditionComplexity-off
// BSLLS:IfElseIfEndsWithElse-off

#Область НастроекМодуля

Функция ЗначениеПоУмолчанию(Наименование)
	
	СписокЗначений = Новый СписокЗначений();
	
	СписокЗначений.Добавить("ИдентификаторПользователяСервиса", "00000000-0000-0000-0000-000000000000");
	СписокЗначений.Добавить("НаименованиеГруппыДоступа", "АРМ Менеджера 1С");
	СписокЗначений.Добавить("НаименованиеГруппыДоступаФизическихЛиц", "Группа доступа по умолчанию");
	СписокЗначений.Добавить("НаименованиеВалюты", "KZT");
	
	Результат = СписокЗначений.НайтиПоЗначению(Наименование);
	
	Если Результат = Неопределено Тогда
		ТекстОшибки = НСтр("ru = 'Отсутствует " + Наименование + "'");
		ВызватьИсключение ТекстОшибки;
	КонецЕсли;
	
	Возврат Результат;
КонецФункции

#КонецОбласти

#Область ОбработкаЗапросов

#Область Тест

Функция ТестGETЗапрос(Запрос)
	
	Возврат ТестGETОтвет(Запрос);
	
КонецФункции

#КонецОбласти
#Область Сотрудники

Функция ТранспортныеСредстваGETЗапрос(Запрос)
	
	Возврат ТранспортныеСредстваGETОтвет(Запрос);
	
КонецФункции

#КонецОбласти

#КонецОбласти

#Область ПолучениеОтветов

#Область Тест

Функция ТестGETОтвет(Запрос)
	СтруктураДанных = Новый Структура;
	СтруктураДанных.Вставить("status", "success");
	
	ЗаписьJSON = Новый ЗаписьJSON;
	ЗаписьJSON.УстановитьСтроку();
	ЗаписатьJSON(ЗаписьJSON, СтруктураДанных);
	
	СтрокаПередаваемыхДанных = ЗаписьJSON.Закрыть();
	
	Ответ = Новый HTTPСервисОтвет(200);
	Ответ.УстановитьТелоИзСтроки(СтрокаПередаваемыхДанных);
	
	Возврат Ответ;
КонецФункции

#КонецОбласти

#Область ТранспортныеСредства

Функция ТранспортныеСредстваGETОтвет(Запрос)
	ДанныеТранспортныхСредств = ПолучитьСправочникТранспортныеСредства();
	
	Ответ = Новый HTTPСервисОтвет(200);
	Ответ.УстановитьТелоИзСтроки(ФорматJSON(ДанныеТранспортныхСредств));
	
	Возврат Ответ;
КонецФункции

#КонецОбласти

#КонецОбласти

#Область Служебные

#Область ТранспортныеСредства

Функция ПолучитьСправочникТранспортныеСредства()
	Запрос = Новый Запрос;
	Запрос.Текст = "ВЫБРАТЬ
		|	ТранспортныеСредства.Код КАК Код,
		|	ТранспортныеСредства.Наименование КАК Наименование
		|ИЗ
		|	Справочник.ТранспортныеСредства КАК ТранспортныеСредства";
	
	Результат = Запрос.Выполнить();
	Выборка = Результат.Выбрать();
	
	МассивТранспортныхСредств = Новый Массив;
	
	Пока Выборка.Следующий() Цикл
		Структура = СтруктураОбъектаСправочникаТранспортныеСредства(Выборка);
		
		МассивТранспортныхСредств.Добавить(Структура);
	КонецЦикла;
	
	Возврат МассивТранспортныхСредств;
	
КонецФункции

Функция СтруктураОбъектаСправочникаТранспортныеСредства(Объект)
	Структура = Новый Структура;
	Структура.Вставить("Ссылка", XMLСтрока(Объект.Ссылка));
	
	Структура.Вставить("Код", Объект.Код);
	Структура.Вставить("Недействителен", Объект.Недействителен);
	
	Возврат Структура;
КонецФункции

#КонецОбласти

#КонецОбласти

#Область Переферийные

Функция ДанныеЗапроса(Запрос)
	ТелоЗапросаКакСтрока = Запрос.ПолучитьТелоКакСтроку();
	ЧтениеJSON = Новый ЧтениеJSON;
	ЧтениеJSON.УстановитьСтроку(ТелоЗапросаКакСтрока);
	ДанныеЗапроса = ПрочитатьJSON(ЧтениеJSON);
	ЧтениеJSON.Закрыть();
	
	Возврат ДанныеЗапроса;
КонецФункции

Функция ФорматJSON(СтруктураДанных)
	ЗаписьJSON = Новый ЗаписьJSON;
	ЗаписьJSON.УстановитьСтроку();
	
	Попытка
		ЗаписатьJSON(ЗаписьJSON, СтруктураДанных);
	Исключение
		СериализаторXDTO.ЗаписатьJSON(ЗаписьJSON, СтруктураДанных);
	КонецПопытки;
	
	Возврат ЗаписьJSON.Закрыть();
КонецФункции

Функция ОшибкаЗапроса(ТекстОшибки, КодВозврата)
	
	СтруктураДанных = Новый Структура;
	СтруктураДанных.Вставить("status", "error");
	СтруктураДанных.Вставить("message", ТекстОшибки);
	
	ЗаписьJSON = Новый ЗаписьJSON;
	ЗаписьJSON.УстановитьСтроку();
	
	ЗаписатьJSON(ЗаписьJSON, СтруктураДанных);
	
	СтрокаПередаваемыхДанных = ЗаписьJSON.Закрыть();
	
	Ответ = Новый HTTPСервисОтвет(КодВозврата);
	Ответ.УстановитьТелоИзСтроки(СтрокаПередаваемыхДанных);
	
	Возврат Ответ;
	
КонецФункции

Функция ЛогинПоФИО(Фамилия, Имя, Отчество)
	
	ПервыйСимволИмени = Лев(Имя, 1);
	
	Если Отчество <> Неопределено Тогда
		Возврат Фамилия + ПервыйСимволИмени;
	Иначе
		ПервыйСимволОтчества = Лев(Отчество, 1);
		Возврат Фамилия + ПервыйСимволИмени + ПервыйСимволОтчества;
	КонецЕсли;
	
КонецФункции

Функция ДатаBitrix(Строка)
	ПозицияТ = СтрНайти(Строка, "T");
	
	Если ПозицияТ > 0 Тогда
		Строка = Лев(Строка, ПозицияТ - 1);
	КонецЕсли;
	
	Строка = СтрЗаменить(Строка, "-", "");
	
	Возврат Дата(Строка);
	
КонецФункции

#КонецОбласти